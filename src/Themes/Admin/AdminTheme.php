<?php
namespace Cubex\Quantum\Themes\Admin;

use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Helpers\Path;

class AdminTheme extends BaseTheme implements DispatchableComponent
{
  const MENU_LEFT = 'menu_left';

  public function __construct()
  {
    ResourceManager::component($this)->requireCss('css/styles.css');
  }

  public function getResourceDirectory()
  {
    return Path::system(__DIR__, 'resources');
  }
}
