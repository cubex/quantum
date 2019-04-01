<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Form\Textarea;

class CkEditorComponent extends Textarea implements DispatchableComponent
{
  protected function _prepareForProduce(): HtmlTag
  {
    $rm = ResourceManager::component($this);
    $rm->requireJs('widget/ckeditor.min.js');
    $rm->requireJs('ckeditor.js');
    $rm->requireCss('ckeditor.css');

    $ele = parent::_prepareForProduce();
    $ele->addClass('content-editor');
    return $ele;
  }

  public function setInline(bool $inline)
  {
    $this->_tag = $inline ? 'div' : 'textarea';
    return $this;
  }
}
