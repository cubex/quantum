<?php
namespace Cubex\Quantum\Base\Components\Menu;

class QuantumMenu
{
  /**
   * @var QuantumMenuItem[]
   */
  protected $_items = [];

  public function addItems(array $items)
  {
    $this->_items = array_merge($this->_items, $items);
    return $this;
  }

  public function addItem(QuantumMenuItem $item)
  {
    $this->_items[] = $item;
    return $this;
  }

  public function removeItem(QuantumMenuItem $item)
  {
    unset($this->_items[array_search($item, $this->_items)]);
    return $this;
  }

  /**
   * @return QuantumMenuItem[]
   */
  public function getItems()
  {
    return $this->_items;
  }
}
