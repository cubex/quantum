<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Quantum\Base\Interfaces\QuantumModule;

class AdminController extends QuantumAdminController
{
  protected function _getConditions()
  {
    yield self::_route('{vendor}/{package}', 'packageHandler');
    return 'dashboard';
  }

  public function processPackageHandler()
  {
    $vendor = $this->getContext()->routeData()->get('vendor');
    $package = $this->getContext()->routeData()->get('package');
    $module = $this->getQuantum()->getModule($vendor, $package);
    if($module instanceof QuantumModule)
    {
      return $module->getAdminHandler();
    }
    throw new \RuntimeException(self::ERROR_NO_ROUTE, 404);
  }

  public function getDashboard()
  {
    $this->_setPageTitle('Admin Dashboard');
    return 'Dashboard';
  }
}
