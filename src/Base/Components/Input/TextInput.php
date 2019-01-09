<?php
namespace Cubex\Quantum\Base\Components\Input;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Form\Input;

class TextInput extends UiElement implements DispatchableComponent
{
  protected $_name;
  protected $_value;

  public static function create($name, $value)
  {
    $o = new static();
    $o->_name = $name;
    $o->_value = $value;
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
      Input::create()
        ->setAttribute('name', $this->_name)
        ->setAttribute('value', $this->_value)
    )->addClass('q-input');
  }
}
