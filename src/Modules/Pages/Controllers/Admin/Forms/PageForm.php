<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin\Forms;

use Cubex\Quantum\Base\Components\CkEditor\CkEditorDecorator;
use Packaged\Form\Csrf\CsrfForm;
use Packaged\Form\DataHandlers\TextDataHandler;

class PageForm extends CsrfForm
{
  public $id;
  /**
   * @var TextDataHandler
   */
  public $path;
  public $version;
  /**
   * @var TextDataHandler
   */
  public $title;
  /**
   * @var TextDataHandler
   */
  public $content;

  protected function _initDataHandlers()
  {
    parent::_initDataHandlers();
    $this->path = new TextDataHandler();
    $this->title = new TextDataHandler();
    $this->content = new TextDataHandler();
    $this->content->setDecorator(new CkEditorDecorator());
  }
}
