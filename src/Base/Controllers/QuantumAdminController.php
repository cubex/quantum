<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Context\Context;
use Cubex\Quantum\Base\Components\Menu\QuantumMenuItem;
use Cubex\Quantum\Themes\Admin\AdminTheme;
use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Helpers\Path;
use PackagedUi\FontAwesome\FaIcon;
use Symfony\Component\HttpFoundation\Response;

abstract class QuantumAdminController extends QuantumBaseController
{
  protected function _createTheme(): BaseTheme
  {
    $theme = $this->getQuantum()->getAdminTheme();
    $theme->getMenu(AdminTheme::MENU_LEFT)
      ->addItem(QuantumMenuItem::create('Dashboard', Path::url($this->getQuantum()->getAdminUri()), FaIcon::HOME));
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
          Path::url($this->getQuantum()->getAdminUri(), $module->getVendor(), $module->getPackage()),
          $module->getIcon()
        )
      );
    }
  }

  protected function _buildModuleUrl(...$parts)
  {
    return Path::url($this->getQuantum()->getAdminUri(), $this->_getVendor(), $this->_getPackage(), ...$parts);
  }

  protected function _getVendor()
  {
    return $this->getContext()->routeData()->get('vendor');
  }

  protected function _getPackage()
  {
    return $this->getContext()->routeData()->get('package');
  }

  public function handle(Context $c): Response
  {
    $this->_applyDefaultMenu();
    return parent::handle($c);
  }
}
