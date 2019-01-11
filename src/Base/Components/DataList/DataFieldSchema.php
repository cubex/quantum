<?php
namespace Cubex\Quantum\Base\Components\DataList;

class DataFieldSchema
{
  public $property;
  public $text;
  public $defaultValue;

  protected $_classes = [];

  public static function create($property, $text, $default = '')
  {
    $o = new static;
    $o->property = $property;
    $o->text = $text;
    $o->defaultValue = $default;
    return $o;
  }

  public function addClass(...$class)
  {
    $this->_classes = array_merge($this->_classes, $class);
    return $this;
  }

  public function getClass()
  {
    return $this->_classes;
  }
}
