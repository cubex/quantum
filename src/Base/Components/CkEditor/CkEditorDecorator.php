<?php
namespace Cubex\Quantum\Base\Components\CkEditor;

use Packaged\Glimpse\Core\CustomHtmlTag;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;
use PackagedUi\Form\Decorators\AbstractDataHandlerDecorator;

class CkEditorDecorator extends AbstractDataHandlerDecorator
{
  protected function _getInputElement(): HtmlTag
  {
    return Div::create(
      CkEditorComponent::create(
        CustomHtmlTag::build('textarea')
          ->setAttribute('name', $this->_handler->getName())
          ->setContent($this->_handler->getValue())
      )
    );
  }
}
