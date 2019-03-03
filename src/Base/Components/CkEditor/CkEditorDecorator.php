<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use PackagedUi\Form\Decorators\AbstractDataHandlerDecorator;

class CkEditorDecorator extends AbstractDataHandlerDecorator
{
  protected function _getInputElement(): HtmlTag
  {
    return Div::create(
      CkEditorComponent::create($this->_handler->getValue())
        ->setAttribute('name', $this->_handler->getName())
    );
  }
}
