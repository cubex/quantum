<?php
namespace Cubex\Quantum\Base\Views;

use Cubex\View\ViewModel;

class ObjectView extends ViewModel
{
  protected $_object;

  function __construct($object)
  {
    $this->_object = $object;
  }

  public function render()
  {
    return print_r($this->_object, true);
  }
}
