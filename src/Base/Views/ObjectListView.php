<?php
namespace Cubex\Quantum\Base\Views;

use Cubex\View\ViewModel;

class ObjectListView extends ViewModel
{
  protected $_objects;

  function __construct(array $objects)
  {
    $this->_objects = $objects;
  }

  public function render()
  {
    return print_r($this->_objects, true);
  }
}
