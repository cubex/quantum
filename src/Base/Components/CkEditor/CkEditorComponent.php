<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Form\Textarea;
use Packaged\Ui\Html\HtmlElement;

class CkEditorComponent extends Textarea implements DispatchableComponent
{
  protected function _prepareForProduce(): HtmlElement
  {
    $this->includeEditorResources();
    $this->includePageResources();

    $ele = parent::_prepareForProduce();
    $ele->addClass('content-editor');
    return $ele;
  }

  public function includePageResources()
  {
    $rm = ResourceManager::componentClass(self::class);
    $rm->requireCss('ckeditor.css');
    $rm->requireCss('styles/styles.css', ['class' => 'ckeditor-style']);
  }

  public function includeEditorResources()
  {
    $rm = ResourceManager::component($this);
    $rm->requireJs('plugin/ckeditor.min.js');
    $rm->requireCss('plugin/ckeditor.min.css');
  }
}
