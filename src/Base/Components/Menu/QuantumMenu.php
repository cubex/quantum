<?php
namespace Cubex\Quantum\Base\Components\Menu;

use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Lists\UnorderedList;
use Packaged\Ui\Element;

class QuantumMenu extends Element
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

  public function render(): string
  {
    $return = UnorderedList::create();
    foreach($this->_items as $item)
    {
      $return->addItem(Link::create($item->getUrl(), $item->getTitle()));
    }
    return $return;
  }
}
