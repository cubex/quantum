<?php
namespace Cubex\Quantum\Services\Pages\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Services\Pages\Daos\PageData;
use Cubex\Quantum\Services\Paths\Daos\Page;

class ContentController extends QuantumBaseController
{
  public function getRoutes()
  {
    return [self::route('', 'default')];
  }

  public function getDefault()
  {
    $page = Page::loadOneWhere(
      [
        'siteId'    => $this->getContext()->config()->getItem('_kubex', 'siteId', 'abc'),
        'path'      => $this->getRequest()->path(),
        'published' => true,
      ]
    );
    if(!$page)
    {
      throw new \Exception('Page Not Found', 404);
    }
    return $this->_defer($page);
  }

  private function _defer(Page $page)
  {
    // defer handler to specified page handler
    $pageData = PageData::loadOneWhere(['siteId' => $page->siteId, 'pageId' => $page->id, 'active' => true]);
    $this->getTheme()->setPageTitle($pageData->title);
    return $pageData->content;
  }
}
