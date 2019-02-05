<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;

class CkEditorComponent extends UiElement implements DispatchableComponent
{
  /**
   * @var HtmlTag
   */
  protected $_input;

  public static function create(HtmlTag $input)
  {
    $o = new static;
    $o->_input = $input;
    return $o;
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }

  public function render(): string
  {
    $rm = ResourceManager::component($this);
    $rm->requireJs('widget/ckeditor.min.js');
    $rm->requireJs('ckeditor.js');
    $rm->requireCss('ckeditor.css');

    return $this->_input->addClass('content-editor');
  }
}
