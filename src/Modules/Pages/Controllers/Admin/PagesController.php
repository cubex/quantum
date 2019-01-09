<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\Input\TextInput;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Modules\Pages\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Modules\Pages\Components\Editor\EditorComponent;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Themes\NoTheme\NoTheme;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableHead;
use Packaged\Glimpse\Tags\Table\TableRow;
use Packaged\Http\Response;

class PagesController extends QuantumAdminController
{
  public function getRoutes()
  {
    return [
      self::route('editor/{pageId@num}/save', 'save'),
      self::route('editor/{pageId@num}/load', 'load'),
      self::route('editor/{pageId@num}', 'editor'),
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
        TableCell::collection(['Title', TextInput::create('title', $page->title)])
      )
    );

    return Div::create(
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

  public function getEditor()
  {
    $this->setTheme(new NoTheme());
    return CkEditorComponent::create();
  }

  public function postSave()
  {
    $pageId = $this->getContext()->routeData()->get('pageId');
    $page = Page::loadById($pageId);
    $page->content = $this->getRequest()->get('data');
    $page->save();
  }

  public function getLoad()
  {
    $pageId = $this->getContext()->routeData()->get('pageId');
    $page = Page::loadById($pageId);
    return Response::create($page->content);
  }
}
