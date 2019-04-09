<?php
namespace Cubex\Quantum\Base\Components\Panel;

use Packaged\Glimpse\Tags\Div;
use Packaged\SafeHtml\ISafeHtmlProducer;
use Packaged\Ui\Element;

class Panel extends Element
{
  /**
   * @var PanelHeader
   */
  protected $_header;

  protected $_content;

  /**
   * @var ISafeHtmlProducer[]
   */
  protected $_actions = [];

  public static function create($content)
  {
    $o = new static();
    $o->_content = $content;
    return $o;
  }

  public function addAction(ISafeHtmlProducer $action)
  {
    $this->_actions[] = $action;
    return $this;
  }

  public function getActions()
  {
    return $this->_actions;
  }

  public function setHeader($header)
  {
    $this->_header = $header;
    return $this;
  }

  public function render(): string
  {
    return Div::create([$this->_header, $this->_content])->addClass('panel');
  }
}
