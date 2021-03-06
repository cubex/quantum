<?php
namespace Cubex\Quantum\Base\Components\Panel;

use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\CustomHtmlTag;
use Packaged\Glimpse\Tags\Lists\UnorderedList;
use Packaged\Glimpse\Tags\Text\HeadingOne;
use Packaged\SafeHtml\ISafeHtmlProducer;
use Packaged\Ui\Element;

class PanelHeader extends Element implements DispatchableComponent
{
  protected $_title;

  /**
   * @var ISafeHtmlProducer[]
   */
  protected $_actions = [];

  public function addAction(ISafeHtmlProducer $action)
  {
    $this->_actions[] = $action;
    return $this;
  }

  public function getActions()
  {
    return $this->_actions;
  }

  public static function create($title)
  {
    $o = new static();
    $o->_title = $title;
    return $o;
  }

  public function render(): string
  {
    ResourceManager::component($this)->requireCss('header.css');

    return CustomHtmlTag::build(
      'header',
      [],
      [
        $this->_title ? HeadingOne::create($this->_title) : null,
        $this->_actions ? UnorderedList::create()->addItems($this->_actions)->addClass('panel-header--actions') : null,
      ]
    )->addClass('panel-header');
  }
}
