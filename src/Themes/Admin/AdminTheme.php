<?php
namespace Cubex\Quantum\Themes\Admin;

use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Layout\Header;
use PackagedUi\FontAwesome\FaIcon;
use PackagedUi\Fusion\Fusion;
use PackagedUi\Fusion\Layout\Drawer\Drawer;
use PackagedUi\Fusion\Menu\Menu;
use PackagedUi\Fusion\Menu\MenuItem;

class AdminTheme extends BaseTheme implements DispatchableComponent
{
  const MENU_LEFT = 'menu_left';

  public function includeResources()
  {
    Fusion::includeGoogleFont();
    $rm = ResourceManager::componentClass(Fusion::class);
    $rm->requireJs(Fusion::FILE_BASE_JS);
    $rm->requireCss(Fusion::FILE_BASE_CSS);
    ResourceManager::vendor('packaged-ui', 'fontawesome')->requireCss(FaIcon::CSS_PATH);

    ResourceManager::componentClass(static::class)->requireCss('css/styles.css');
  }

  public function getHeader()
  {
    return Header::create(
      FaIcon::create(FaIcon::BARS)->fixedWidth()->sizeLarge()
        ->addClass('pin-menu', Fusion::DRAWER_TOGGLE)
    );
  }

  protected function _drawerContent()
  {
    $menu = Menu::create();
    foreach($this->getMenu(self::MENU_LEFT)->getItems() as $item)
    {
      $menu->appendContent(
        MenuItem::create($item->getTitle())
          ->setHref($item->getUrl())
          ->setLeading(FaIcon::create($item->getIcon()))
          ->setAttribute('title', $item->getTitle())
      );
    }
    return $menu;
  }

  public function getContent(bool $withDrawer = null)
  {
    $content = parent::getContent();
    if($withDrawer !== true)
    {
      return $content;
    }
    return Drawer::create($this->_drawerContent())
      ->setState(Drawer::STATE_NARROW)
      ->setReveal(Drawer::REVEAL_PEEK)
      ->setAppContent($this->_content);
  }
}
