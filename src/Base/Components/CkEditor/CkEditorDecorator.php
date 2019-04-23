<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Form\Decorators\AbstractDataHandlerDecorator;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Ui\Html\HtmlElement;

class CkEditorDecorator extends AbstractDataHandlerDecorator
{
  protected function _initInputElement(): HtmlTag
  {
    return CkEditorComponent::create();
  }

  protected function _configureInputElement(HtmlElement $input)
  {
    parent::_configureInputElement($input);
    if($input instanceof HtmlTag)
    {
      $input->setContent($this->_handler->getValue());
    }
  }
}
