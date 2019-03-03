<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Quantum\Base\Interfaces\QuantumModule;

class FrontendController extends QuantumBaseController
{
  public function getRoutes()
  {
    yield self::route('/{vendor}/{package}', 'packageHandler');
    return $this->getQuantum()->contentHandler();
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
    throw new \RuntimeException(self::ERROR_NO_ROUTE, 404);
  }
}
