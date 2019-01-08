<?php
namespace Cubex\Quantum\Modules\Pages\Components\Editor;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Form\Input;
use Packaged\Helpers\Strings;

class EditorComponent extends UiElement implements DispatchableComponent
{
  protected $_url;

  public static function create($url)
  {
    $o = new static;
    $o->_url = $url;
    return $o;
  }

  public function render(): string
  {
    $rm = ResourceManager::component($this);
    $rm->requireCss('editor.css');

    $id = 'ge-' . Strings::randomString(20);
    return Div::create(
      [
        Input::create()->setId($id)->setAttribute('type', 'hidden')->setAttribute('value', 'test'),
        HtmlTag::createTag('iframe', ['src' => $this->_url, 'gi' => $id]),
      ]
    );
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }
}
