<?php
namespace Cubex\Quantum\Base;

use Cubex\Context\Context;
use Cubex\Cubex;
use Cubex\Quantum\Base\Controllers\AdminController;
use Cubex\Quantum\Base\Controllers\FrontendController;
use Cubex\Quantum\Base\Interfaces\QuantumAware;
use Cubex\Quantum\Base\Interfaces\QuantumModule;
use Cubex\Quantum\Base\Uri\Uri;
use Cubex\Quantum\Modules\Pages\PagesModule;
use Cubex\Quantum\Modules\Paths\Controllers\PathRouteController;
use Cubex\Quantum\Modules\Paths\PathsModule;
use Cubex\Quantum\Themes\Admin\AdminTheme;
use Cubex\Quantum\Themes\BaseTheme;
use Cubex\Quantum\Themes\Quantifi\QuantifiTheme;
use Cubex\Routing\Router;
use Packaged\Config\Provider\Ini\IniConfigProvider;
use Packaged\Dal\DalResolver;
use Packaged\Dal\Foundation\Dao;
use Packaged\Dispatch\Dispatch;
use Packaged\Helpers\Path;
use Symfony\Component\HttpFoundation\Response;

abstract class QuantumProject
{
  /**
   * @var Cubex
   */
  private $_cubex;
  private $_frontendModules = [];
  private $_adminModules = [];
  /**
   * @var QuantumModule[] module class name
   */
  private $_modules = [];

  public function getAdminUri(): Uri
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

  protected $_resourcePath = '/_r';

  public function __construct(Cubex $cubex)
  {
    $this->_cubex = $cubex;
    $context = $cubex->getContext();

    $cubex->listen(
      Cubex::EVENT_HANDLE_PRE_EXECUTE,
      function ($context, $handler) {
        if($handler instanceof QuantumAware)
        {
          $handler->setQuantum($this);
        }
      }
    );

    Dispatch::bind(new Dispatch($context->getProjectRoot(), $this->_resourcePath));
    Dispatch::instance()->addComponentAlias('\Cubex\Quantum', 'quantum');

    // configure dal
    $cnf = new IniConfigProvider(
      Path::system(
        $context->getProjectRoot(),
        'conf',
        'defaults',
        'connections.ini'
      )
    );
    $resolver = new DalResolver($cnf);
    Dao::setDalResolver($resolver);

    $this->_init();

    //add built in modules
    $this->addModule(new PathsModule());
    $this->addModule(new PagesModule());
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

  abstract protected function _init();

  /**
   * @param bool $send
   * @param bool $catch
   *
   * @return Response
   * @throws \Throwable
   */
  public function handle($send = true, $catch = true)
  {
    $router = Router::i();
    $router->handleFunc(
      $this->_resourcePath,
      function (Context $c) {
        return Dispatch::instance()->handle($c->getRequest());
      }
    );
    $adminPath = $this->getAdminUri();
    if($adminPath)
    {
      $router->handle((string)$adminPath, new AdminController());
    }
    $router->handle('/', new FrontendController());
    return $this->_cubex->handle($router, $send, $catch);
  }
}

