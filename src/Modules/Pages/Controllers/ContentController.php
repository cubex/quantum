<?php
namespace Cubex\Quantum\Modules\Pages\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\Interfaces\QuantumFrontendHandler;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Symfony\Component\HttpFoundation\ParameterBag;

class ContentController extends QuantumBaseController implements QuantumFrontendHandler
{
  /**
   * @var ParameterBag
   */
  protected $_options;

  public function setOptions(ParameterBag $options)
  {
    $this->_options = $options;
  }

  public function getRoutes()
  {
    return [self::route('', 'default')];
  }

  public function processDefault()
  {
    $pageData = Page::loadById($this->_options->get('pageId'));
    $this->getTheme()->setPageTitle($pageData->title);
    return $pageData->content;
  }
}
