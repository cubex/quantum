<?php
namespace Cubex\Quantum\Base\Controllers;

use Cubex\Context\Context;
use Cubex\Controller\Controller;
use Cubex\Quantum\Base\Interfaces\QuantumAware;
use Cubex\Quantum\Base\Traits\QuantumAwareTrait;
use Cubex\Quantum\Themes\BaseTheme;
use Packaged\SafeHtml\ISafeHtmlProducer;
use Packaged\SafeHtml\SafeHtml;
use Packaged\Ui\Renderable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class QuantumBaseController extends Controller implements QuantumAware
{
  use QuantumAwareTrait;

  private $_pageTitle;
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
    return $this->getQuantum()->getFrontendTheme();
  }

  protected function _setPageTitle(string $pageTitle)
  {
    $this->_pageTitle = $pageTitle;
    return $this;
  }

  private function _getPageTitle(): string
  {
    return $this->_pageTitle ?: '';
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
      $theme = $this->getQuantum()->getErrorTheme();
      $theme->setPageTitle($e->getMessage())
        ->setCode($e->getCode())
        ->setContent($e->getMessage());
      return Response::create($theme);
    }
  }

  protected function _prepareResponse(Context $c, $result, $buffer = null)
  {
    if(is_string($result) || $result instanceof Renderable || $result instanceof ISafeHtmlProducer)
    {
      if($result instanceof Renderable)
      {
        $result = new SafeHtml($result->render());
      }
      if($result instanceof ISafeHtmlProducer)
      {
        $result = $result->produceSafeHTML();
      }

      $theme = $this->getTheme();
      $theme->setPageTitle($this->_getPageTitle());
      $result = $theme->setContent($result);
    }
    return parent::_prepareResponse($c, $result);
  }
}
