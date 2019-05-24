<?php
namespace Cubex\Quantum\Base;

use Cubex\Application\Application;
use Cubex\Cubex;
use Cubex\Events\Handle\ResponsePreSendHeadersEvent;
use Cubex\Events\PreExecuteEvent;
use Cubex\Quantum\Base\Components\CkEditor\CkEditorComponent;
use Cubex\Quantum\Base\Dispatch\QuantumDispatch;
use Cubex\Quantum\Base\FileStore\DiskStore\DiskFileStore;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\Interfaces\QuantumAware;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Base\Uri\Uri;
use Cubex\Quantum\Modules\Pages\PagesModule;
use Cubex\Quantum\Modules\Paths\Controllers\PathRouteController;
use Cubex\Quantum\Modules\Paths\PathsModule;
use Cubex\Quantum\Modules\Upload\UploadModule;
use Cubex\Quantum\Themes\Admin\AdminTheme;
use Cubex\Quantum\Themes\BaseTheme;
use Cubex\Quantum\Themes\ErrorTheme\ErrorTheme;
use Cubex\Quantum\Themes\Quantifi\QuantifiTheme;
use Packaged\Config\Provider\Ini\IniConfigProvider;
use Packaged\Context\Context;
use Packaged\Dal\DalResolver;
use Packaged\Dal\Foundation\Dao;
use Packaged\Dispatch\Dispatch;
use Packaged\Dispatch\Resources\ResourceFactory;
use Packaged\Helpers\Path;
use Packaged\Http\Response;
use Packaged\Routing\Handler\FuncHandler;
use Packaged\Routing\Handler\Handler;

abstract class QuantumProject extends Application
{
  private $_adminTheme;
  private $_frontendTheme;
  private $_errorTheme;
  private $_frontendModules = [];
  private $_adminModules = [];
  /**
   * @var QuantumModule[][] module class name
   */
  private $_modules = [];

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
    $this->getCubex()
      ->factory(
        'upload-' . FileStoreInterface::class,
        function () {
          $config = $this->getContext()->config()->getSection('upload');
          $config->addItem('project_root', $this->getContext()->getProjectRoot());
          return (new DiskFileStore())->configure($config);
        }
      )
      ->share(CkEditorComponent::class, CkEditorComponent::class);
  }

  protected function _generateRoutes()
  {
    foreach(["favicon.ico", "robots.txt"] as $resource)
    {
      yield self::_route(
        "/" . $resource,
        new FuncHandler(
          function (Context $c) use ($resource) {
            return ResourceFactory::fromFile(
              Path::system($c->getProjectRoot(), 'public', $resource)
            );
          }
        )
      );
    }

    $dispatch = $this->_prepareDispatch($this->getCubex()->getContext()->getProjectRoot());
    Dispatch::bind($dispatch);
    yield self::_route(QuantumDispatch::PATH, $dispatch);

    return parent::_generateRoutes();
  }

  protected function _prepareDispatch(string $projectRoot): Dispatch
  {
    return new QuantumDispatch($projectRoot, QuantumDispatch::PATH);
  }

  protected function _defaultHandler(): Handler
  {
    return new QuantumDefaultHandler();
  }

  public function getAdminUri(): ?Uri
  {
    return Uri::create('/admin');
  }

  public function contentHandler()
  {
    return PathRouteController::class;
  }

  final public function getAdminTheme(): BaseTheme
  {
    if(!$this->_adminTheme)
    {
      $this->_adminTheme = $this->_getNewAdminTheme();
    }
    return $this->_adminTheme;
  }

  protected function _getNewAdminTheme()
  {
    return new AdminTheme();
  }

  final public function getFrontendTheme(): BaseTheme
  {
    if(!$this->_frontendTheme)
    {
      $this->_frontendTheme = $this->_getNewFrontendTheme();
    }
    return $this->_frontendTheme;
  }

  protected function _getNewFrontendTheme(): BaseTheme
  {
    return new QuantifiTheme();
  }

  final public function getErrorTheme(): ErrorTheme
  {
    if(!$this->_errorTheme)
    {
      $this->_errorTheme = $this->_getNewErrorTheme();
    }
    return $this->_errorTheme;
  }

  protected function _getNewErrorTheme()
  {
    return new ErrorTheme();
  }

  protected function _initialize()
  {
    //Send debug headers locally
    $this->getCubex()->listen(
      ResponsePreSendHeadersEvent::class,
      function (ResponsePreSendHeadersEvent $e) {
        $r = $e->getResponse();
        if($r instanceof Response && $e->getContext()->isEnv(Context::ENV_LOCAL))
        {
          $r->enableDebugHeaders();
        }
      }
    );

    // add quantum to any executing handler
    $this->getContext()->events()->listen(
      PreExecuteEvent::class,
      function (PreExecuteEvent $event) {
        $handler = $event->getHandler();
        if($handler instanceof QuantumAware)
        {
          $handler->setQuantum($this);
        }
      }
    );

    $this->_configureDal();

    $this->addModule(new PathsModule());
    $this->_configureModules();
  }

  protected function _configureDal()
  {
    $projectRoot = $this->getCubex()->getContext()->getProjectRoot();
    // configure dal
    $cnf = (new IniConfigProvider())->loadFiles(
      [
        Path::system($projectRoot, 'conf', 'defaults', 'connections.ini'),
        Path::system($projectRoot, 'conf', $this->getCubex()->getContext()->getEnvironment(), 'connections.ini'),
      ],
      true,
      false
    );

    Dao::setDalResolver(new DalResolver($cnf));
  }

  public function addModule(QuantumModule $class)
  {
    $this->_modules[$class->getVendor()][$class->getPackage()] = $class;
    if($class->hasAdmin())
    {
      $this->_adminModules[$class->getVendor()][$class->getPackage()] = true;
    }
  }

  public function getModule(string $vendor, string $package)
  {
    return $this->_modules[$vendor][$package] ?? null;
  }

  public function removeModule(string $vendor, string $package)
  {
    unset($this->_adminModules[$vendor][$package], $this->_frontendModules[$vendor][$package]);
  }

  /**
   * @return QuantumModule[]
   */
  public function getAdminModules()
  {
    $modules = [];
    foreach($this->_adminModules as $vendor => $packages)
    {
      foreach($packages as $package => $enabled)
      {
        if($enabled)
        {
          $modules[] = $this->getModule($vendor, $package);
        }
      }
    }
    return $modules;
  }

  protected function _configureModules()
  {
    //add built in modules
    $this->addModule(new UploadModule());
    $this->addModule(new PagesModule());
  }
}

