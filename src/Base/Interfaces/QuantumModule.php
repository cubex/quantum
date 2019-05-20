<?php
namespace Cubex\Quantum\Base\Interfaces;

use Packaged\Routing\Handler\Handler;

interface QuantumModule
{
  public function getName($language = 'en'): string;

  public function getIcon(): string;

  public function getVendor(): string;

  public function getPackage(): string;

  public function hasAdmin(): bool;

  public function getAdminHandler(): Handler;

  public function getFrontendHandler(): Handler;

}
