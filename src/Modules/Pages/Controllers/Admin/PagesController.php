<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Base\Components\Pagination\Pagination;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Modules\Pages\Controllers\Admin\Forms\PageForm;
use Cubex\Quantum\Modules\Pages\Controllers\ContentController;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Pages\Daos\PageContent;
use Cubex\Quantum\Modules\Paths\PathHelper;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Lists\ListItem;
use Packaged\Glimpse\Tags\Lists\OrderedList;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Text\StrongText;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Packaged\SafeHtml\SafeHtml;
use PackagedUi\FontAwesome\FaIcon;
use PackagedUi\Fusion\ButtonInferface;
use PackagedUi\Fusion\Card\Card;
use PackagedUi\Fusion\Fusion;
use PackagedUi\Fusion\Layout\Flex;
use PackagedUi\Fusion\Layout\FlexGrow;
use PackagedUi\Fusion\Table\Table;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagesController extends QuantumAdminController
{
  const SESSION_ID = '';

  public function getRoutes()
  {
    yield  self::route('_image', 'image');
    yield  self::route('publish/{pageId@num}/{version@num}', 'publish');
    yield  self::route('live/{pageId@num}/{version@num}', 'live');
    yield  self::route('live/{pageId@num}', 'live');
    yield  self::route('{pageId@num}/{version@num}', 'edit');
    yield  self::route('{pageId@num}', 'edit');
    yield  self::route('new', 'edit');
    return 'list';
  }

  public function getLive()
  {
    $page = Page::loadById($this->routeData()->get('pageId'));
    $content = $this->_getPageContent($page, $this->routeData()->get('version'));

    if($content->theme)
    {
      $this->setTheme(new $content->theme);
    }
    else
    {
      $this->setTheme($this->getQuantum()->getFrontendTheme());
    }
    $this->getTheme()->setPageTitle($content->title);
    return CkEditorComponent::create(new SafeHtml($content->content))->setInline();
  }

  public function postImage()
  {
    $class = $this->getContext()->config()->getItem('upload', 'class');
    /** @var FileStoreInterface $uploadClass */
    $uploadClass = new $class();

    $file = $this->request()->files->get('upload');
  }

  public function getList()
  {
    $this->_setPageTitle('Pages');

    $pageNumber = (int)$this->request()->get('page', 0);
    $limit = 10;
    /** @var  $pages */
    $pages = Page::collection();
    $pageCount = $pages->count();

    $pages->limitWithOffset($pageNumber * $limit, $limit);

    $table = Table::create()->striped()
      ->addHeaderRow('Path', 'Title', '');

    /** @var Page $page */
    foreach($pages as $page)
    {
      $content = $this->_getPageContent($page);
      $table->addRow(
        $page->path,
        $content->title,
        TableCell::create(
          Link::create($this->_buildModuleUrl($page->id), FaIcon::create(FaIcon::EDIT))
        )->addClass('shrink')
      );
    }

    return Card::create($table)
      ->setHeader(
        Flex::create(
          FlexGrow::create("Pages"),
          Link::create($this->_buildModuleUrl('new'), FaIcon::create(FaIcon::PLUS))
        )
      )
      ->setFooter(
        Div::create(
          Pagination::create(
            $pageCount,
            $limit,
            $pageNumber,
            function ($p) { return $this->_buildModuleUrl() . '?page=' . $p; }
          )
        )->addClass(Fusion::TEXT_RIGHT)
      );
  }

  public function getEdit()
  {
    $this->_setPageTitle('Edit Page');

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

    $form = new PageForm(self::SESSION_ID);
    $form->setAction($postUrl);
    $form->id = $page->id;
    $form->path = $page->path;
    $form->version = $content->id;
    $form->title = $content->title;
    $form->content = $content->content;

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

    $request = $this->request();

    $page->path = $request->get('path');
    $page->save();

    $content->pageId = $page->id;
    $content->title = $request->get('title');
    $content->content = $request->get('content');
    if($content->save())
    {
      // contents changed, redirect to new edit url
      return RedirectResponse::create($this->_buildModuleUrl($page->id, $content->id));
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
  private function _getPageContent(Page $page, $version = null)
  {
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
   * @return OrderedList
   */
  protected function _getVersionList(Page $page)
  {
    // version history
    $versionList = OrderedList::create();
    $versions = PageContent::each(EqualPredicate::create('pageId', $page->id));
    foreach($versions as $version)
    {
      $publishLink = null;
      if($page->publishedVersion !== $version->id)
      {
        $publishLink = Link::create($this->_buildModuleUrl('publish', $page->id, $version->id), 'PUBLISH')
          ->addClass(ButtonInferface::BUTTON);
      }
      $versionList->addItem(
        ListItem::create(
          [
            Link::create(
              $this->_buildModuleUrl($page->id, $version->id),
              '[' . date('Y-m-d H:i:s', $version->createdTime) . '] ' . $version->title
              . ' (' . strlen($version->content) . ' bytes)'
            ),
            $publishLink,
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
