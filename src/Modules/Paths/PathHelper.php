<?php
namespace Cubex\Quantum\Modules\Paths;

use Cubex\Quantum\Modules\Paths\Daos\Path;
use Packaged\Dal\Exceptions\Connection\DuplicateKeyException;
use Packaged\QueryBuilder\Predicate\EqualPredicate;

class PathHelper
{
  public static function addPath($path, $handlerModule, $handlerOptions)
  {
    $pathObj = static::getPath($path);
    if($pathObj)
    {
      throw new DuplicateKeyException('Path already exists');
    }
    static::setPath($path, $handlerModule, $handlerOptions);
  }

  public static function setPath($path, $handlerModule, $handlerOptions)
  {
    $pathObj = new Path();
    $pathObj->path = $path;
    $pathObj->handlerModule = $handlerModule;
    $pathObj->handlerOptions = $handlerOptions;
    $pathObj->save();
  }

  public static function getPath($path)
  {
    return Path::loadOneWhere(EqualPredicate::create('path', $path));
  }
}