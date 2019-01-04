<?php
namespace Cubex\Quantum\Base\Interfaces;

use Cubex\Http\Handler;

interface QuantumModule
{
  public function getName($language = 'en'): string;

  public function getVendor(): string;

  public function getPackage(): string;

  public function hasAdmin(): bool;

  public function getAdminHandler(): Handler;

  public function getFrontendHandler(): Handler;

}
