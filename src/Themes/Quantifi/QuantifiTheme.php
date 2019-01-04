<?php
namespace Cubex\Quantum\Themes\Quantifi;

use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Helpers\Path;

class QuantifiTheme extends BaseTheme implements DispatchableComponent
{
  public function __construct()
  {
    ResourceManager::component($this)->requireCss('css/styles.css');
  }

  public function getResourceDirectory()
  {
    return Path::system(__DIR__, 'resources');
  }
}
