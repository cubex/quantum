<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\Input\TextInput;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Modules\Pages\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Modules\Pages\Components\EditorIframe\EditorIframeComponent;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Pages\Daos\PageContent;
use Cubex\Quantum\Themes\NoTheme\NoTheme;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Lists\ListItem;
use Packaged\Glimpse\Tags\Lists\OrderedList;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableHead;
use Packaged\Glimpse\Tags\Table\TableRow;
use Packaged\Glimpse\Tags\Text\StrongText;
use Packaged\QueryBuilder\Predicate\EqualPredicate;

class PagesController extends QuantumAdminController
{
  public function getRoutes()
  {
    return [
      self::route('editor/{pageId@num}', 'contentEditor'),
      self::route('{pageId@num}/{version@num}', 'edit'),
      self::route('{pageId@num}', 'edit'),
      self::route('', 'list'),
    ];
  }

  public function getList()
  {
    $this->_applyDefaultMenu();

    /** @var  $pages */
    $pages = Page::collection()->limitWithOffset(0, 10);

    $table = Table::create();
    $table->appendContent($row = TableRow::create());
    $row->appendContent(TableHead::create()->appendContent(TableCell::collection(['ID', 'Title'])));
    /** @var Page $page */
    foreach($pages as $page)
    {
      $content = $this->_getPageContent($page);

      $table->appendContent($row = TableRow::create());
      $row->appendContent(
        TableCell::collection(
          [$page->id, Link::create($this->_buildModuleUrl($page->id), $content->title ?: '- No Title -')]
        )
      );
    }
    return $table;
  }

  public function getEdit()
  {
    $this->_applyDefaultMenu();

    $page = Page::loadById($this->getContext()->routeData()->get('pageId'));
    $content = $this->_getPageContent($page, $this->getContext()->routeData()->get('version'));

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

    $form = HtmlTag::createTag(
      'form',
      ['action' => $this->_buildModuleUrl($page->id), 'method' => 'post'],
      [
        $table,
        EditorIframeComponent::create(
          $this->_buildModuleUrl('editor', $page->id),
          HtmlTag::createTag('textarea')->setContent($content->content)
            ->setAttributes(['name' => 'content', 'style' => 'display:none'])
        ),
        HtmlTag::createTag('button', [], 'Submit'),
      ]
    );

    // version history
    $versionList = OrderedList::create();
    $versions = PageContent::each(EqualPredicate::create('pageId', $page->id));
    foreach($versions as $version)
    {
      $versionList->addItem(
        ListItem::create(
          Link::create(
            $this->_buildModuleUrl($page->id, $version->id),
            '[' . date('Y-m-d H:i:s', $version->createdTime) . '] ' . $version->title
            . ' (' . strlen($version->content) . ' bytes)'
          )
        )
      );
    }

    return Div::create([$form, StrongText::create('Versions'), $versionList]);
  }

  public function postEdit()
  {
    // todo: CSRF
    $pageId = $this->getContext()->routeData()->get('pageId');

    $page = Page::loadById($pageId);
    $page->path = $this->getRequest()->get('path');
    $page->save();

    $content = $this->_getPageContent($page);
    $content->title = $this->getRequest()->get('title');
    $content->content = $this->getRequest()->get('content');
    $content->save();
    return $this->getEdit();
  }

  public function getContentEditor()
  {
    $this->setTheme(new NoTheme());
    return CkEditorComponent::create();
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
}
