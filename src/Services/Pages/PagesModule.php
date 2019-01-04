<?php
namespace Cubex\Quantum\Services\Pages;

use Cubex\Http\Handler;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Services\Pages\Controllers\Admin\PagesController;
use Cubex\Quantum\Services\Pages\Controllers\ContentController;
use Cubex\Routing\HttpConstraint;
use Cubex\Routing\Route;

class PagesModule implements QuantumModule
{
  public function getName($language = 'en'): string
  {
    return 'Pages';
  }

  public function getVendor(): string
  {
    return 'quantum';
  }

  public function getPackage(): string
  {
    return 'pages';
  }

  public function getAdminHandler(): Handler
  {
    return new PagesController();
  }

  public function getFrontendHandler(): Handler
  {
    return new ContentController();
  }

  public function hasAdmin(): bool
  {
    return true;
  }
}
