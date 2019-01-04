<?php
namespace Cubex\Quantum\Services\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\Menu\QuantumMenuItem;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;

class PagesController extends QuantumAdminController
{
  public function defaultAction()
  {
    return 'Pages';
  }

  public function getTitle()
  {
    return 'Pages';
  }

  public function getRoutes()
  {
    return [self::route('', 'default')];
  }

  public function getMenuItems()
  {
    return [QuantumMenuItem::create('test', '/bll')];
  }

  protected function _init()
  {
    parent::_init();
    $this->_addMenuItem('pages', PagesController::class);
  }

  public function getDefault()
  {
    $this->_applyDefaultMenu();

    return 'page admin';
  }
}
