<?php
namespace Cubex\Quantum\Modules\Pages\Components\CkEditor;

use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use Packaged\SafeHtml\SafeHtml;

class CkEditorComponent extends UiElement implements DispatchableComponent
{
  /**
   * @var Page
   */
  protected $_page;

  public static function create(Page $page)
  {
    $o = new static;
    $o->_page = $page;
    return $o;
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }

  public function render(): string
  {
    $rm = ResourceManager::component($this);
    $rm->requireJs('balloon/ckeditor.min.js');
    $rm->requireCss('editor.css');
    $rm->requireJs('ckeditor.js');
    return HtmlTag::createTag(
      'html',
      [],
      HtmlTag::createTag(
        'body',
        ['class' => 'loading'],
        Div::create(
          [
            Div::create(new SafeHtml($this->_page->content))->setId('content-editor'),
            Div::create('WIDGETS')->setId('widgets'),
          ]
        )->setId('wrapper')
      )
    );
  }
}
