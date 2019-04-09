<?php
namespace Cubex\Quantum\Themes\Quantifi;

use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;

class QuantifiTheme extends BaseTheme implements DispatchableComponent
{
  public function __construct()
  {
    ResourceManager::component($this)->requireCss('css/styles.css');
  }
}
