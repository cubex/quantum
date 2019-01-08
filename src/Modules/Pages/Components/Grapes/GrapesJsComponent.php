<?php
namespace Cubex\Quantum\Modules\Pages\Components\Grapes;

use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Core\HtmlTag;
use Packaged\Glimpse\Tags\Div;

class GrapesJsComponent extends UiElement implements DispatchableComponent
{
  /**
   * @var Page
   */
  protected $_page;

  public static function create(Page $page)
  {
    $o = new static;
    $o->_page = $page;
    return $o;
  }

  public function render(): string
  {
    $rm = ResourceManager::component($this);
    $rm->requireCss('grapes/css/grapes.min.css');
    $rm->requireJs('grapes/grapes.min.js');
    $rm->requireCss('grapes-newsletter/grapesjs-preset-newsletter.css');
    $rm->requireJs('grapes-newsletter/grapesjs-preset-newsletter.min.js');
    $rm->requireCss('grapes.css');
    $rm->requireJs('grapes.js');

    return HtmlTag::createTag(
      'html',
      [],
      HtmlTag::createTag(
        'body',
        ['class' => 'loading'],
        Div::create($this->_page->content)->setId('content-editor')
      )
    );
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }
}
