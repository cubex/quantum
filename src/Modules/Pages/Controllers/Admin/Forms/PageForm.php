<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin\Forms;

use Packaged\Form\Csrf\CsrfForm;
use Packaged\Form\DataHandlers\ReadOnlyDataHandler;
use Packaged\Form\DataHandlers\TextDataHandler;

class PageForm extends CsrfForm
{
  /**
   * @var ReadOnlyDataHandler
   */
  public $id;
  /**
   * @var TextDataHandler
   */
  public $path;
  /**
   * @var ReadOnlyDataHandler
   */
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
    $this->id = new ReadOnlyDataHandler();
    $this->version = new ReadOnlyDataHandler();
    $this->path = new TextDataHandler();
    $this->title = new TextDataHandler();
    $this->content = new TextDataHandler();
    $this->setDecorator(new PageFormDecorator());
  }
}
