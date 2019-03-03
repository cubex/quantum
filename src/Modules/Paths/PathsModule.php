<?php
namespace Cubex\Quantum\Modules\Paths;

use Cubex\Http\Handler;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Modules\Paths\Controllers\PathRouteController;

class PathsModule implements QuantumModule
{
  public function getName($language = 'en'): string
  {
    return 'Paths';
  }

  public function getIcon(): string
  {
    return '';
  }

  public function getVendor(): string
  {
    return 'quantum';
  }

  public function getPackage(): string
  {
    return 'paths';
  }

  public function hasAdmin(): bool
  {
    return false;
  }

  public function getAdminHandler(): Handler
  {
    return null;
  }

  public function getFrontendHandler(): Handler
  {
    return new PathRouteController();
  }
}
