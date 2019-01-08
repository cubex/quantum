<?php
namespace Cubex\Quantum\Modules\Pages\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Paths\Daos\Path;

class ContentController extends QuantumBaseController
{
  public function getRoutes()
  {
    return [self::route('', 'default')];
  }

  public function getDefault()
  {
    $page = Path::loadOneWhere(
      [
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

  private function _defer(Path $page)
  {
    // defer handler to specified page handler
    $pageData = Page::loadOneWhere(['pageId' => $page->id, 'active' => true]);
    $this->getTheme()->setPageTitle($pageData->title);
    return $pageData->content;
  }
}
