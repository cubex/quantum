<?php
namespace Cubex\Quantum\Base\Components\Menu;

class QuantumMenuItem
{
  protected $_title;
  protected $_url;
  protected $_icon;

  public static function create($title, $url, $icon)
  {
    $object = new static;
    $object->_title = $title;
    $object->_url = $url;
    $object->_icon = $icon;
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

  public function getIcon()
  {
    return $this->_icon;
  }
}
