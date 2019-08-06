<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Dispatch\ResourceStore;
use Packaged\Glimpse\Tags\Form\Textarea;
use Packaged\Ui\Html\HtmlElement;

class CkEditorComponent extends Textarea implements DispatchableComponent
{
  /**
   * @var ResourceStore
   */
  private $_iframeStore;

  public function __construct(...$content)
  {
    $this->_iframeStore = new ResourceStore();
    parent::__construct($content);
  }

  public function getIframeResourceStore()
  {
    return $this->_iframeStore;
  }

  protected function _prepareForProduce(): HtmlElement
  {
    // editor resources
    $rm = ResourceManager::componentClass(self::class);
    $rm->requireCss('ckeditor.css');
    $this->includeEditorResources();

    // within iframe styles
    $resources = $this->_iframeStore->getResources(ResourceStore::TYPE_CSS);
    $uris = json_encode(array_keys($resources));
    ResourceManager::inline()->requireJs('window.Quantum.Editor.Init(\'.content-editor\',null,' . $uris . ')');

    $ele = parent::_prepareForProduce();
    $ele->addClass('content-editor');
    return $ele;
  }

  public function includeEditorResources()
  {
    $rm = ResourceManager::component($this);
    $rm->requireJs('plugin/ckeditor.min.js');
    $rm->requireCss('plugin/ckeditor.min.css');
  }

  public function includePageResources()
  {
    $rm = ResourceManager::component($this);
    $rm->requireCss('plugin/ckeditor.min.css');
  }
}
