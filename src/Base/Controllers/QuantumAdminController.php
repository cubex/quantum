<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Quantum\Base\Components\Menu\QuantumMenuItem;
use Cubex\Quantum\Themes\Admin\AdminTheme;
use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Helpers\Path;

abstract class QuantumAdminController extends QuantumBaseController
{
  protected function _createTheme(): BaseTheme
  {
    $theme = new AdminTheme();

    $theme->getMenu(AdminTheme::MENU_LEFT)->addItem(
      QuantumMenuItem::create('Dashboard', Path::url($this->getQuantum()->getAdminPath()))
    );
    return $theme;
  }

  public function canProcess()
  {
    // todo: security
    return true;
  }

  protected function _applyDefaultMenu()
  {
    foreach($this->getQuantum()->getAdminModules() as $module)
    {
      $this->getTheme()->getMenu(AdminTheme::MENU_LEFT)->addItem(
        QuantumMenuItem::create(
          $module->getName(),
          Path::url($this->getQuantum()->getAdminPath(), $module->getVendor(), $module->getPackage())
        )
      );
    }
  }

  protected function _buildModuleUrl(...$parts)
  {
    $vendor = $this->getContext()->routeData()->get('vendor');
    $package = $this->getContext()->routeData()->get('package');
    return Path::url($this->getQuantum()->getAdminPath(), $vendor, $package, ...$parts);
  }
}
