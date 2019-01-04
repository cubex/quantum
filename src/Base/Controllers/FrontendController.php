<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Exception;

class FrontendController extends QuantumBaseController
{
  public function getRoutes()
  {
    $routes = [];
    $routes[] = self::route('/_/{vendor}/{package}', 'packageHandler');
    $routes[] = self::route('/', $this->getQuantum()->contentHandler());
    return $routes;
  }

  public function processPackageHandler()
  {
    $vendor = $this->getContext()->routeData()->get('vendor');
    $package = $this->getContext()->routeData()->get('package');
    $module = $this->getQuantum()->getModule($vendor, $package);
    if($module instanceof QuantumModule)
    {
      return $module->getFrontendHandler();
    }
    throw new Exception("Fucked");
  }
}
