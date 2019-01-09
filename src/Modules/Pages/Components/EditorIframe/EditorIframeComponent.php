<?php
namespace Cubex\Quantum\Modules\Pages\Components\EditorIframe;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use Packaged\Helpers\Strings;

class EditorIframeComponent extends UiElement implements DispatchableComponent
{
  protected $_url;
  protected $_input;

  public static function create($url, $input)
  {
    $o = new static;
    $o->_url = $url;
    $o->_input = $input;
    return $o;
  }

  public function render(): string
  {
    $rm = ResourceManager::component($this);
    $rm->requireCss('editor.css');

    $id = 'ge-' . Strings::randomString(20);
    return Div::create(
      [
        $this->_input->setId($id)->setAttribute('type', 'hidden'),
        HtmlTag::createTag('iframe', ['src' => $this->_url, 'gi' => $id]),
      ]
    );
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }
}
