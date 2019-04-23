<?php

namespace Cubex\Quantum\Base;

use Cubex\Quantum\Base\Controllers\AdminController;
use Cubex\Quantum\Base\Controllers\FrontendController;
use Cubex\Quantum\Base\Controllers\QuantumBaseController;

class QuantumDefaultHandler extends QuantumBaseController
{
  protected function _generateRoutes()
  {
    $adminPath = $this->getQuantum()->getAdminUri();
    if($adminPath)
    {
      yield self::_route((string)$adminPath, AdminController::class);
    }
    yield self::_route('/', FrontendController::class);
  }
}
