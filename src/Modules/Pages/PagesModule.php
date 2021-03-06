<?php
namespace Cubex\Quantum\Modules\Pages;

use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Modules\Pages\Controllers\Admin\PagesController;
use Cubex\Quantum\Modules\Pages\Controllers\ContentController;
use Packaged\Routing\Handler\Handler;
use PackagedUi\FontAwesome\FaIcon;

class PagesModule implements QuantumModule
{
  public function getName($language = 'en'): string
  {
    return 'Pages';
  }

  public function getIcon(): string
  {
    return FaIcon::BOOK;
  }

  public function getVendor(): string
  {
    return 'quantum';
  }

  public function getPackage(): string
  {
    return 'pages';
  }

  public function hasAdmin(): bool
  {
    return true;
  }

  public function getAdminHandler(): Handler
  {
    return new PagesController();
  }

  public function getFrontendHandler(): Handler
  {
    return new ContentController();
  }
}
