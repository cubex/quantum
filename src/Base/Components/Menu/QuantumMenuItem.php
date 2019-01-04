<?php
namespace Cubex\Quantum\Base\Components\Menu;

class QuantumMenuItem
{
  protected $_title;
  protected $_url;

  public static function create($title, $url)
  {
    $object = new static;
    $object->_title = $title;
    $object->_url = $url;
    return $object;
  }

  public function getTitle()
  {
    return $this->_title;
  }

  public function getUrl()
  {
    return $this->_url;
  }
}
