<?php
namespace Cubex\Quantum\Modules\Pages\Controllers\Admin;

use Cubex\Quantum\Base\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Base\Components\CkEditor\CkEditorDecorator;
use Cubex\Quantum\Base\Components\Pagination\Pagination;
use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Modules\Pages\Controllers\Admin\Forms\PageForm;
use Cubex\Quantum\Modules\Pages\Controllers\ContentController;
use Cubex\Quantum\Modules\Pages\Daos\Page;
use Cubex\Quantum\Modules\Pages\Daos\PageContent;
use Cubex\Quantum\Modules\Paths\PathHelper;
use Packaged\Dispatch\Dispatch;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Text\StrongText;
use Packaged\QueryBuilder\Expression\Like\StartsWithExpression;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Packaged\QueryBuilder\Predicate\LikePredicate;
use Packaged\SafeHtml\SafeHtml;
use PackagedUi\FontAwesome\FaIcon;
use PackagedUi\Fusion\ButtonInferface;
use PackagedUi\Fusion\Card\Card;
use PackagedUi\Fusion\Fusion;
use PackagedUi\Fusion\Layout\Flex;
use PackagedUi\Fusion\Layout\FlexGrow;
use PackagedUi\Fusion\Table\Table;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagesController extends QuantumAdminController
{
  const SESSION_ID = '';

  protected function _generateRoutes()
  {
    yield  self::_route('_image', 'image');
    yield  self::_route('publish/{pageId@num}/{version@num}', 'publish');
    yield  self::_route('live/{pageId@num}/{version@num}', 'live');
    yield  self::_route('live/{pageId@num}', 'live');
    yield  self::_route('{pageId@num}/{version@num}', 'edit');
    yield  self::_route('{pageId@num}', 'edit');
    yield  self::_route('new', 'edit');
    return 'list';
  }

  public function getLive()
  {
    $page = Page::loadById($this->routeData()->get('pageId'));
    $content = $this->_getPageContent($page, $this->routeData()->get('version'));

    if($content->theme)
    {
      $this->setTheme(new $content->theme());
    }
    else
    {
      $this->setTheme($this->getQuantum()->getFrontendTheme());
    }
    $this->getTheme()->setPageTitle($content->title);

    return $this->getContext()->getCubex()->retrieve(CkEditorComponent::class, [new SafeHtml($content->content)]);
  }

  public function postImage()
  {
    $class = $this->getContext()->config()->getItem('upload', 'class');
    /** @var FileStoreInterface $uploadClass */
    $uploadClass = new $class();

    $file = $this->request()->files->get('upload');
  }

  public function getList()
  {
    $this->_setPageTitle('Pages');

    $pageNumber = (int)$this->request()->get('page', 0);
    $limit = 10;
    /** @var  $pages */

    $where = [];
    $pathSearch = $this->request()->query->get('path');
    if($pathSearch)
    {
      $where[] = LikePredicate::create('path', StartsWithExpression::create($pathSearch));
    }

    $pages = Page::collection($where)->orderBy('path');

    $pageCount = $pages->count();

    $pages->limitWithOffset($pageNumber * $limit, $limit);

    $table = Table::create()->striped()
      ->addHeaderRow('Path', 'Title', '');

    /** @var Page $page */
    foreach($pages as $page)
    {
      $content = $this->_getPageContent($page);
      $table->addRow(
        $page->path,
        $content->title,
        TableCell::create(
          Link::create($this->_buildModuleUrl($page->id), FaIcon::create(FaIcon::EDIT))
        )->addClass('shrink')
      );
    }

    return Card::create($table)
      ->setHeader(
        Flex::create(
          FlexGrow::create("Pages"),
          Link::create($this->_buildModuleUrl('new'), FaIcon::create(FaIcon::PLUS))
        )
      )
      ->setFooter(
        Div::create(
          Pagination::create(
            $pageCount,
            $limit,
            $pageNumber,
            function ($p) { return $this->_buildModuleUrl() . '?page=' . $p; }
          )
        )->addClass(Fusion::TEXT_RIGHT)
      );
  }

  public function getEdit()
  {
    $this->_setPageTitle('Edit Page');

    $pageId = $this->getContext()->routeData()->get('pageId');
    $version = $this->getContext()->routeData()->get('version');

    if($pageId)
    {
      $page = Page::loadById($pageId);
      $content = $this->_getPageContent($page, $version);
      $postUrl = $this->_buildModuleUrl($page->id, $content->id);
    }
    else
    {
      $page = new Page();
      $content = new PageContent();
      $postUrl = $this->_buildModuleUrl('new');
    }

    $globalStore = Dispatch::instance()->store();
    /** @var CkEditorComponent $ckComponent */
    $ckComponent = $this->getContext()->getCubex()->retrieve(CkEditorComponent::class);
    Dispatch::instance()->setResourceStore($ckComponent->getIframeResourceStore());

    if($content->theme)
    {
      $theme = new $content->theme();
    }
    else
    {
      $theme = $this->getQuantum()->getFrontendTheme();
    }
    $theme->includeResources();

    Dispatch::instance()->setResourceStore($globalStore);

    ResourceManager::vendor('packaged', 'form')->requireCss('assets/form.css');

    $form = new PageForm(self::SESSION_ID);
    $form->setAction($postUrl);
    $form->id = $page->id;
    $form->path = $page->path;
    $form->version = $content->id;
    $form->title = $content->title;
    $form->content = $content->content;
    $form->content->setDecorator(new CkEditorDecorator($ckComponent));

    return Div::create(
      [
        $form,
        StrongText::create('Versions'),
        $this->_getVersionList($page),
      ]
    );
  }

  public function postEdit()
  {
    // todo: CSRF
    $pageId = $this->getContext()->routeData()->get('pageId');

    if($pageId)
    {
      $page = Page::loadById($pageId);
      $content = $this->_getPageContent($page, $this->getContext()->routeData()->get('version'));
    }
    else
    {
      $page = new Page();
      $content = new PageContent();
    }

    $request = $this->request();

    $page->path = $request->get('path');
    $page->save();

    $content->pageId = $page->id;
    $content->title = $request->get('title');
    $content->content = $request->get('content');
    if($content->save())
    {

      if($this->request()->request->get('_submit', 'Save') === 'Save & Publish')
      {
        $this->_publish($content->pageId, $content->id);
      }

      // contents changed, redirect to new edit url
      return RedirectResponse::create($this->_buildModuleUrl($page->id, $content->id));
    }
    // no changes, just show same page
    return $this->getEdit();
  }

  /**
   * @param Page   $page
   * @param string $version Leave empty for current published version, or latest version if not yet published
   *
   * @return PageContent
   */
  private function _getPageContent(Page $page, $version = null)
  {
    if($version)
    {
      $content = PageContent::collection(
        EqualPredicate::create('pageId', $page->id),
        EqualPredicate::create('id', $version)
      )->first();
    }
    else
    {
      $content = PageContent::collection(EqualPredicate::create('pageId', $page->id))
        ->orderBy(['id' => 'DESC'])
        ->first();
    }
    if(!$content)
    {
      $content = new PageContent();
      $content->pageId = $page->id;
    }
    return $content;
  }

  protected function _getVersionList(Page $page)
  {
    // version history
    $versionList = Table::create()->striped();
    $versions = PageContent::collection(EqualPredicate::create('pageId', $page->id))
      ->orderBy(['createdTime' => 'DESC']);
    foreach($versions as $version)
    {
      $publishLink = null;
      if($page->publishedVersion !== $version->id)
      {
        $publishLink = Link::create($this->_buildModuleUrl('publish', $page->id, $version->id), 'PUBLISH')
          ->addClass(ButtonInferface::BUTTON);
      }
      $versionList->addRow(
        Link::create(
          $this->_buildModuleUrl($page->id, $version->id),
          '[' . date('Y-m-d H:i:s', $version->createdTime) . '] ' . $version->title
          . ' (' . strlen($version->content) . ' bytes)'
        ),
        $publishLink
      );
    }
    return $versionList;
  }

  public function getPublish()
  {
    [$page, $changes] = $this->_publish(
      $this->getContext()->routeData()->get('pageId'),
      $this->getContext()->routeData()->get('version')
    );

    if(!empty($changes['publishedPath']['from']))
    {
      PathHelper::removePath($changes['publishedPath']['from'], ContentController::class);
    }
    PathHelper::setPath($page->publishedPath, ContentController::class, new ParameterBag(['pageId' => $page->id]));

    return RedirectResponse::create($this->_buildModuleUrl($page->id));
  }

  protected function _publish($pageId, $version)
  {
    $page = Page::loadById($pageId);
    $page->publishedVersion = $version;
    $page->publishedPath = $page->path;
    $changes = $page->save();
    return [$page, $changes];
  }
}
