<?php
namespace Cubex\Quantum\Modules\Upload;

use Cubex\Http\Handler;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Modules\Upload\Controllers\UploadController;
use Cubex\Quantum\Modules\Upload\Controllers\UploadFrontendController;
use PackagedUi\FontAwesome\FaIcon;

class UploadModule implements QuantumModule
{
  public function getName($language = 'en'): string
  {
    return 'Upload';
  }

  public function getIcon(): string
  {
    return FaIcon::UPLOAD;
  }

  public function getVendor(): string
  {
    return 'quantum';
  }

  public function getPackage(): string
  {
    return 'upload';
  }

  public function hasAdmin(): bool
  {
    return true;
  }

  public function getAdminHandler(): Handler
  {
    return new UploadController();
  }

  public function getFrontendHandler(): Handler
  {
    return new UploadFrontendController();
  }
}
