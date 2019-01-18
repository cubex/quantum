<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\DataList\DataFieldSchema;
use Cubex\Quantum\Base\Components\DataList\DataList;
use Cubex\Quantum\Base\Components\DataList\DataSchema;
use Cubex\Quantum\Base\Components\Input\TextInput;
use Cubex\Quantum\Base\Components\Panel\Panel;
use Cubex\Quantum\Base\Components\Panel\PanelHeader;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Modules\FileStore\FileStorageInterface;
use Cubex\Quantum\Modules\Pages\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Modules\Pages\Controllers\ContentController;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Pages\Daos\PageContent;
use Cubex\Quantum\Modules\Paths\PathHelper;
use Packaged\Glimpse\Core\CustomHtmlTag;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Lists\ListItem;
use Packaged\Glimpse\Tags\Lists\OrderedList;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableRow;
use Packaged\Glimpse\Tags\Text\StrongText;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Packaged\SafeHtml\ISafeHtmlProducer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagesController extends QuantumAdminController
{
  public function getRoutes()
  {
    return [
      self::route('publish/{pageId@num}/{version@num}', 'publish'),
      self::route('{pageId@num}/{version@num}', 'edit'),
      self::route('{pageId@num}', 'edit'),
      self::route('new', 'edit'),
      self::route('', 'list'),
    ];
  }

  public function getList()
  {
    $this->_applyDefaultMenu();

    /** @var  $pages */
    $pages = Page::collection()->limitWithOffset(0, 10);

    $data = [];
    /** @var Page $page */
    foreach($pages as $page)
    {
      $data[] = [
        'page'    => $page->getDaoPropertyData(),
        'content' => $this->_getPageContent($page)->getDaoPropertyData(),
        'actions' => [Link::create($this->_buildModuleUrl($page->id), 'Edit')],
      ];
    }

    $schema = DataSchema::create($data)
      ->addField(DataFieldSchema::create('actions', '')->addClass('shrink'))
      //->addField(DataFieldSchema::create('page.id', 'ID')->addClass('shrink'))
      ->addField(DataFieldSchema::create('page.path', 'Path'))
      //->addField(DataFieldSchema::create('page.publishedPath', 'Published'))
      ->addField(DataFieldSchema::create('content.title', 'Title'));

    $header = PanelHeader::create('Pages')
      ->addAction(Link::create($this->_buildModuleUrl('new'), 'New'));
    return Panel::create(DataList::create($schema))->setHeader($header);
  }

  public function getEdit()
  {
    $this->_applyDefaultMenu();

    $pageId = $this->getContext()->routeData()->get('pageId');
    $version = $this->getContext()->routeData()->get('version');

    if($pageId)
    {
      $page = Page::loadById($pageId);
      $content = $this->_getPageContent($page, $version);
      $postUrl = $this->_buildModuleUrl($page->id, $content->id);
    }
    else
    {
      $page = new Page();
      $content = new PageContent();
      $postUrl = $this->_buildModuleUrl('new');
    }

    $table = Table::create();
    $table->appendContent(TableRow::create()->appendContent(TableCell::collection(['ID', $page->id])));
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Path', TextInput::create('path', $page->path)])
      )
    );
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Version', $content->id])
      )
    );
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Title', TextInput::create('title', $content->title)])
      )
    );

    $form = CustomHtmlTag::build(
      'form',
      ['action' => $postUrl, 'method' => 'post'],
      [
        $table,
        CkEditorComponent::create(
          CustomHtmlTag::build('textarea')->setContent($content->content)
            ->setAttributes(['name' => 'content'])
        ),
        CustomHtmlTag::build('button', [], 'Submit'),
      ]
    );

    return Div::create(
      [
        $form,
        StrongText::create('Versions'),
        $this->_getVersionList($page),
      ]
    );
  }

  public function postEdit()
  {
    // todo: CSRF
    $pageId = $this->getContext()->routeData()->get('pageId');

    if($pageId)
    {
      $page = Page::loadById($pageId);
      $content = $this->_getPageContent($page, $this->getContext()->routeData()->get('version'));
    }
    else
    {
      $page = new Page();
      $content = new PageContent();
    }
    $page->path = $this->getRequest()->get('path');
    $page->save();

    $content->pageId = $page->id;
    $content->title = $this->getRequest()->get('title');
    $content->content = $this->getRequest()->get('content');
    if($content->save())
    {
      // contents changed, redirect to new edit url
      return RedirectResponse::create($this->_buildModuleUrl($page->id));
    }
    // no changes, just show same page
    return $this->getEdit();
  }

  /**
   * @param Page   $page
   * @param string $version Leave empty for current published version, or latest version if not yet published
   *
   * @return PageContent
   */
  private function _getPageContent(Page $page, $version = '')
  {
    $version = $version ?: $page->publishedVersion;
    if($version)
    {
      $content = PageContent::collection(
        EqualPredicate::create('pageId', $page->id),
        EqualPredicate::create('id', $version)
      )->first();
    }
    else
    {
      $content = PageContent::collection(EqualPredicate::create('pageId', $page->id))
        ->orderBy(['id' => 'DESC'])
        ->first();
    }
    if(!$content)
    {
      $content = new PageContent();
      $content->pageId = $page->id;
    }
    return $content;
  }

  /**
   * @param Page $page
   *
   * @return ISafeHtmlProducer
   */
  protected function _getVersionList(Page $page): ISafeHtmlProducer
  {
    // version history
    $versionList = OrderedList::create();
    $versions = PageContent::each(EqualPredicate::create('pageId', $page->id));
    foreach($versions as $version)
    {
      $publishButton = CustomHtmlTag::build('button', [], 'PUBLISH');
      $publishLink = Link::create(
        $this->_buildModuleUrl('publish', $page->id, $version->id),
        $publishButton
      );
      if($page->publishedVersion === $version->id)
      {
        $publishLink->removeAttribute('href');
        $publishButton->setAttribute('disabled', true);
      }
      $versionList->addItem(
        ListItem::create(
          [
            $publishLink,
            ' ',
            Link::create(
              $this->_buildModuleUrl($page->id, $version->id),
              '[' . date('Y-m-d H:i:s', $version->createdTime) . '] ' . $version->title
              . ' (' . strlen($version->content) . ' bytes)'
            ),
          ]
        )
      );
    }
    return $versionList;
  }

  public function getPublish()
  {
    $page = Page::loadById($this->getContext()->routeData()->get('pageId'));
    $page->publishedVersion = $this->getContext()->routeData()->get('version');
    $page->publishedPath = $page->path;
    $changes = $page->save();

    if(!empty($changes['publishedPath']['from']))
    {
      PathHelper::removePath($changes['publishedPath']['from'], ContentController::class);
    }
    PathHelper::setPath($page->publishedPath, ContentController::class, new ParameterBag(['pageId' => $page->id]));

    return RedirectResponse::create($this->_buildModuleUrl($page->id));
  }
}
