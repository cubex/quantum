<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Context\Context;
use Cubex\Controller\Controller;
use Cubex\Quantum\Base\Interfaces\QuantumAware;
use Cubex\Quantum\Base\QuantumProject;
use Cubex\Quantum\Themes\BaseTheme;
use Cubex\Quantum\Themes\Quantifi\QuantifiTheme;
use Packaged\SafeHtml\ISafeHtmlProducer;
use Packaged\Ui\Renderable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class QuantumBaseController extends Controller implements QuantumAware
{
  protected $_quantum;

  public function setQuantum(QuantumProject $project)
  {
    $this->_quantum = $project;
    return $this;
  }

  public function getQuantum(): QuantumProject
  {
    return $this->_quantum;
  }

  private $_theme;

  /**
   * @return BaseTheme
   */
  final public function getTheme()
  {
    if(!$this->_theme)
    {
      $this->_theme = $this->_createTheme();
    }
    return $this->_theme;
  }

  public function setTheme(BaseTheme $theme)
  {
    $this->_theme = $theme;
    return $this;
  }

  protected function _createTheme(): BaseTheme
  {
    return new QuantifiTheme();
  }

  protected function _init()
  {
  }

  public function handle(Context $c): Response
  {
    try
    {
      return parent::handle($c);
    }
    catch(Throwable $e)
    {
      $theme = $this->getTheme();
      $theme->setPageTitle($e->getMessage())->setContent($e->getMessage());
      return Response::create($theme, $e->getCode() ?: 500);
    }
  }

  protected function _prepareResponse(Context $c, $obj)
  {
    if($obj instanceof QuantumAware)
    {
      $obj->setQuantum($this->getQuantum());
    }
    if($obj instanceof ISafeHtmlProducer)
    {
      $obj = $obj->produceSafeHTML()->getContent();
    }
    if($obj instanceof Renderable)
    {
      $obj = $obj->render();
    }
    if(is_string($obj))
    {
      $theme = $this->getTheme();
      $obj = $theme->setContent($obj);
    }
    return parent::_prepareResponse($c, $obj);
  }
}
