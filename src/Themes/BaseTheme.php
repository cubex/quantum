<?php
namespace Cubex\Quantum\Themes;

use Cubex\Quantum\Base\Components\Menu\QuantumMenu;
use Packaged\Ui\Element;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class BaseTheme extends Element
{
  protected $_data;

  /**
   * @return ParameterBag
   */
  public function data()
  {
    if(!$this->_data)
    {
      $this->_data = new ParameterBag();
    }
    return $this->_data;
  }

  /**
   * @var QuantumMenu[]
   */
  protected $_menu;

  /**
   * @param string $menuName menu identifier
   *
   * @return QuantumMenu
   */
  public function getMenu($menuName)
  {
    if(!isset($this->_menu[$menuName]))
    {
      $this->_menu[$menuName] = new QuantumMenu();
    }
    return $this->_menu[$menuName];
  }

  protected $_content;

  public function setContent($content)
  {
    $this->_content = $content;
    return $this;
  }

  public function getContent()
  {
    return $this->_content;
  }

  protected $_pageTitle;

  public function setPageTitle($title)
  {
    $this->_pageTitle = $title;
    return $this;
  }

  public function getPageTitle()
  {
    return $this->_pageTitle;
  }

  public function render(): string
  {
    return parent::render();
  }
}
