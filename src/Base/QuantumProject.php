<?php
namespace Cubex\Quantum\Base;

use Cubex\Context\Context;
use Cubex\Cubex;
use Cubex\Http\LazyHandler;
use Cubex\Quantum\Base\Controllers\AdminController;
use Cubex\Quantum\Base\Controllers\FrontendController;
use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\Dispatch\QuantumDispatch;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Base\Uri\Uri;
use Cubex\Quantum\Modules\Pages\PagesModule;
use Cubex\Quantum\Modules\Paths\Controllers\PathRouteController;
use Cubex\Quantum\Modules\Paths\PathsModule;
use Cubex\Quantum\Modules\Upload\UploadModule;
use Cubex\Quantum\Themes\Admin\AdminTheme;
use Cubex\Quantum\Themes\BaseTheme;
use Cubex\Quantum\Themes\Quantifi\QuantifiTheme;
use Cubex\Routing\Router;
use Packaged\Config\Provider\Ini\IniConfigProvider;
use Packaged\Dal\DalResolver;
use Packaged\Dal\Foundation\Dao;
use Packaged\Dispatch\Dispatch;
use Packaged\Dispatch\Resources\ResourceFactory;
use Packaged\Helpers\Path;

abstract class QuantumProject extends Router
{
  /**
   * @var Cubex
   */
  private $_cubex;
  private $_frontendModules = [];
  private $_adminModules = [];
  /**
   * @var QuantumModule[][] module class name
   */
  private $_modules = [];

  public function getAdminUri(): ?Uri
  {
    return Uri::create('/admin');
  }

  public function contentHandler()
  {
    return PathRouteController::class;
  }

  public function getAdminTheme(): BaseTheme
  {
    return new AdminTheme();
  }

  public function getFrontendTheme(): BaseTheme
  {
    return new QuantifiTheme();
  }

  public function __construct(Cubex $cubex)
  {
    $this->_cubex = $cubex;
    $projectRoot = $cubex->getContext()->getProjectRoot();
    $dispatch = new QuantumDispatch($projectRoot, QuantumDispatch::PATH);
    Dispatch::bind($dispatch);
    $this->handle(QuantumDispatch::PATH, $dispatch);

    $this->_configureRoutes();

    $adminPath = $this->getAdminUri();
    if($adminPath)
    {
      $this->handle(
        (string)$adminPath,
        new LazyHandler(
          function () {
            return $this->handleController(new AdminController());
          }
        )
      );
    }

    $this->handle(
      "/",
      new LazyHandler(
        function () {
          return $this->handleController(new FrontendController());
        }
      )
    );
  }

  public function handleController(QuantumBaseController $c)
  {
    $c->setQuantum($this);
    $cubex = $this->_cubex;
    $context = $cubex->getContext();

    // configure dal
    $cnf = new IniConfigProvider(Path::system($context->getProjectRoot(), 'conf', 'defaults', 'connections.ini'));
    try
    {
      $cnf->loadFile(
        Path::system($context->getProjectRoot(), 'conf', $context->getEnvironment(), 'connections.ini'),
        true
      );
    }
    catch(\RuntimeException $e)
    {
    }

    $resolver = new DalResolver($cnf);
    Dao::setDalResolver($resolver);

    //add built in modules
    $this->addModule(new UploadModule());
    $this->addModule(new PathsModule());
    $this->addModule(new PagesModule());

    $this->_configureModules();

    return $c;
  }

  public function getCubex()
  {
    return $this->_cubex;
  }

  public function getContext()
  {
    return $this->getCubex()->getContext();
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

  protected function _configureModules() { }

  protected function _configureRoutes()
  {
    foreach(["favicon.ico", "robots.txt"] as $resource)
    {
      $this->handleFunc(
        "/" . $resource,
        function (Context $c) use ($resource) {
          return ResourceFactory::fromFile(
            Path::system($c->getProjectRoot(), 'public', $resource)
          );
        }
      );
    }
  }
}

