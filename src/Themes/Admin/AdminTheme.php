<?php
namespace Cubex\Quantum\Themes\Admin;

use Cubex\Quantum\Themes\BaseTheme;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Layout\Header;
use Packaged\Helpers\Path;
use PackagedUi\FontAwesome\FaIcon;
use PackagedUi\Fusion\Fusion;
use PackagedUi\Fusion\Layout\Drawer\Drawer;
use PackagedUi\Fusion\Menu\Menu;
use PackagedUi\Fusion\Menu\MenuItem;

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

  public function getHeader()
  {
    return Header::create(
      FaIcon::create(FaIcon::BARS)->fixedWidth()->sizeLarge()
        ->addClass('pin-menu', Fusion::DRAWER_TOGGLE, Fusion::DRAWER_HIDE_MOBILE)
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

  public function getContent(bool $withDrawer = false)
  {
    $content = parent::getContent();
    if(!$withDrawer)
    {
      return $content;
    }
    return Drawer::create($this->_drawerContent())
      ->setState(Drawer::STATE_NARROW)
      ->setReveal(Drawer::REVEAL_PEEK)
      ->setAppContent($this->_content);
  }

}
