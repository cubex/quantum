<?php
namespace Cubex\Quantum\Modules\Paths\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Modules\Paths\Daos\Path;

class PathRouteController extends QuantumBaseController
{
  public function getRoutes()
  {
    return [self::route('', 'default')];
  }

  public function processDefault()
  {
    $page = Path::loadOneWhere(
      [
        'path'      => $this->getRequest()->path(),
        'published' => true,
      ]
    );
    if(!$page)
    {
      throw new \Exception('Page Not Found', 404);
    }

    return $page->handler;
  }
}
