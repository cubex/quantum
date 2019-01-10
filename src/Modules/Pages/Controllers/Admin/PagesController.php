<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\Input\TextInput;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Modules\Pages\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Modules\Pages\Components\EditorIframe\EditorIframeComponent;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Themes\NoTheme\NoTheme;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableHead;
use Packaged\Glimpse\Tags\Table\TableRow;

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
      $table->appendContent($row = TableRow::create());
      $row->appendContent(
        TableCell::collection([$page->id, Link::create($this->_buildModuleUrl($page->id), $page->title)])
      );
    }
    return $table;
  }

  public function getEdit()
  {
    $this->_applyDefaultMenu();

    $pageId = $this->getContext()->routeData()->get('pageId');
    $page = Page::loadById($pageId);

    $table = Table::create();
    $table->appendContent(TableRow::create()->appendContent(TableCell::collection(['ID', $page->id])));
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Path', TextInput::create('path', '')])
      )
    );
    $table->appendContent(
      TableRow::create()->appendContent(
        TableCell::collection(['Title', TextInput::create('title', $page->title)])
      )
    );

    return HtmlTag::createTag(
      'form',
      ['action' => $this->_buildModuleUrl($pageId), 'method' => 'post'],
      [
        $table,
        EditorIframeComponent::create(
          $this->_buildModuleUrl('editor', $pageId),
          HtmlTag::createTag('textarea')->setContent($page->content)
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
    $page->title = $this->getRequest()->get('title');
    $page->content = $this->getRequest()->get('content');
    $page->save();
    return $this->getEdit();
  }

  public function getContentEditor()
  {
    $this->setTheme(new NoTheme());
    return CkEditorComponent::create();
  }
}
