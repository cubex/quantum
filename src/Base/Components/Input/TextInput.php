<?php
namespace Cubex\Quantum\Base\Components\Input;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Form\Input;

class TextInput extends UiElement implements DispatchableComponent
{
  protected $_vendor;
  protected $_package;
  protected $_content;

  public static function create($vendor, $package, $content)
  {
    $o = new static();
    $o->_vendor = $vendor;
    $o->_package = $package;
    $o->_content = $content;
    return $o;
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resource';
  }

  public function render(): string
  {
    ResourceManager::component($this)->requireJs('input.js');

    return Div::create(
      Input::create()->setAttribute('value', $this->_content)
    )->addClass('q-input');
  }
}
