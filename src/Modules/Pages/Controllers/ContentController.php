<?php
namespace Cubex\Quantum\Modules\Pages\Controllers;

use Cubex\Quantum\Base\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\Interfaces\QuantumFrontendHandler;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Pages\Daos\PageContent;
use Packaged\Dispatch\ResourceManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class ContentController extends QuantumBaseController implements QuantumFrontendHandler
{
  /**
   * @var ParameterBag
   */
  protected $_options;

  protected function _generateRoutes()
  {
    return 'default';
  }

  public function setOptions(ParameterBag $options)
  {
    $this->_options = $options;
  }

  public function processDefault()
  {
    $editorComponent = $this->getContext()->getCubex()->retrieve(CkEditorComponent::class);
    ResourceManager::component($editorComponent)->requireCss('styles/styles.css');

    $page = Page::loadById($this->_options->get('pageId'));
    $content = PageContent::loadById($page->id, $page->publishedVersion);

    if($content->theme)
    {
      $this->setTheme(new $content->theme);
    }

    $this->getTheme()->setPageTitle($content->title);
    return $content->content;
  }
}
