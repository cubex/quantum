<?php
namespace Cubex\Quantum\Modules\Paths;

use Cubex\Quantum\Base\Interfaces\QuantumFrontendHandler;
use Cubex\Quantum\Modules\Paths\Daos\Path;
use Exception;
use Packaged\Dal\Exceptions\Connection\DuplicateKeyException;
use Packaged\QueryBuilder\Predicate\EqualPredicate;
use Symfony\Component\HttpFoundation\ParameterBag;

class PathHelper
{
  public static function addPath($path, $handlerModule, ParameterBag $handlerOptions)
  {
    self::_assertValidHandler($handlerModule);
    $pathObj = static::getPath($path);
    if($pathObj)
    {
      throw new DuplicateKeyException('Path already exists');
    }
    static::setPath($path, $handlerModule, $handlerOptions);
  }

  public static function setPath($path, $handlerModule, ParameterBag $handlerOptions)
  {
    self::_assertValidHandler($handlerModule);
    $pathObj = Path::loadOrNew($path);
    $pathObj->handlerModule = $handlerModule;
    $pathObj->handlerOptions = $handlerOptions;
    $pathObj->save();
  }

  public static function getPath($path)
  {
    return Path::loadOneWhere(EqualPredicate::create('path', $path));
  }

  public static function removePath($path, $handlerModule)
  {
    Path::collection(
      EqualPredicate::create('path', $path),
      EqualPredicate::create('handlerModule', $handlerModule)
    )->delete();
  }

  /**
   * @param $handlerModule
   *
   * @throws Exception
   */
  private static function _assertValidHandler($handlerModule): void
  {
    if(!is_subclass_of($handlerModule, QuantumFrontendHandler::class))
    {
      throw new Exception('Invalid handler');
    }
  }
}
