<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin\Forms;

use Cubex\Quantum\Base\Components\CkEditor\CkEditorDecorator;
use PackagedUi\Form\Csrf\CsrfForm;
use PackagedUi\Form\DataHandlers\TextDataHandler;

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

  /**
   * @var string
   */
  private $_action;

  /**
   * @param string $action
   *
   * @return $this
   */
  public function setAction($action)
  {
    $this->_action = $action;
    return $this;
  }

  public function getAction()
  {
    return $this->_action;
  }

  protected function _initDataHandlers()
  {
    parent::_initDataHandlers();
    $this->path = new TextDataHandler();
    $this->title = new TextDataHandler();
    $this->content = new TextDataHandler();
    $this->content->setDecorator(new CkEditorDecorator());
  }
}
