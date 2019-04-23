<?php
namespace Cubex\Quantum\Modules\Paths\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\Interfaces\QuantumFrontendHandler;
use Cubex\Quantum\Modules\Paths\Daos\Path;

class PathRouteController extends QuantumBaseController
{
  protected function _generateRoutes()
  {
    return 'default';
  }

  public function processDefault()
  {
    $path = Path::loadOneWhere(
      [
        'path' => $this->request()->path(),
      ]
    );
    if(!$path)
    {
      throw new \Exception('Page Not Found', 404);
    }

    // defer to specified page handler
    $module = new $path->handlerModule();
    if($module instanceof QuantumFrontendHandler)
    {
      $module->setOptions($path->handlerOptions);
    }
    return $module;
  }
}
