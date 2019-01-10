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
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableHead;
use Packaged\Glimpse\Tags\Table\TableRow;
use Packaged\QueryBuilder\Predicate\EqualPredicate;

class PagesController extends QuantumAdminController
{
  public function getRoutes()
  {
    return [
      self::route('editor/{pageId@num}', 'contentEditor'),
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
      $content = $this->_getCurrentContent($page);

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
    $content = $this->_getCurrentContent($page);

    $table = Table::create();
    $table->appendContent(TableRow::create()->appendContent(TableCell::collection(['ID', $page->id])));
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Path', TextInput::create('path', $page->path)])
      )
    );
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Title', TextInput::create('title', $content->title)])
      )
    );

    return HtmlTag::createTag(
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
  }

  public function postEdit()
  {
    // todo: CSRF
    $pageId = $this->getContext()->routeData()->get('pageId');

    $page = Page::loadById($pageId);
    $page->path = $this->getRequest()->get('path');
    $page->save();

    $content = $this->_getCurrentContent($page);
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
   * @param Page $page
   *
   * @return PageContent
   */
  private function _getCurrentContent(Page $page)
  {
    if($page->publishedVersion)
    {
      $content = PageContent::collection(
        EqualPredicate::create('pageId', $page->id),
        EqualPredicate::create('id', $page->publishedVersion)
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
