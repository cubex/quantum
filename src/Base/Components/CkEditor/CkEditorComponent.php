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
    $rm = ResourceManager::component($this);
    $rm->requireJs('ckeditor.min.js');
    $rm->requireCss('ckeditor.css');

    $ele = parent::_prepareForProduce();
    $ele->addClass('content-editor');
    return $ele;
  }

  public function setInline(bool $inline = true)
  {
    $this->_tag = $inline ? 'div' : 'textarea';
    return $this;
  }
}
