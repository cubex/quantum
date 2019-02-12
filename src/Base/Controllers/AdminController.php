<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Exception;

class AdminController extends QuantumAdminController
{
  public function getRoutes()
  {
    yield self::route('{vendor}/{package}', 'packageHandler');
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
    throw new Exception("Fucked");
  }

  public function getDashboard()
  {
    $this->_applyDefaultMenu();
    $this->getTheme()->setPageTitle('admin title');
    return 'ADMIN';
  }
}
