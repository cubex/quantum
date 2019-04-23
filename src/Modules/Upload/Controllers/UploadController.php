<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Cubex\Quantum\Modules\Upload\Filer\FilerObject;
use Packaged\Config\ConfigSectionInterface;
use Packaged\Helpers\Objects;
use Packaged\Http\Response;

class UploadController extends QuantumBaseController
{
  protected function _generateRoutes()
  {
    yield self::_route('{path@all}', 'upload');
    return 'upload';
  }

  public function getUpload()
  {
    $store = $this->_getStore();
    $path = $this->getContext()->routeData()->get('path', '');

    $pathObj = $store->getObject($path);
    $list = [];
    if($pathObj->isDir())
    {
      /** @var FileStoreObjectInterface[] $list */
      try
      {
        $list = $store->list($path);
        $list = array_reverse(Objects::msort($list, 'isDir'));
      }
      catch(FileStoreException $e)
      {
        return Response::create($e->getMessage(), 400);
      }
    }

    $return = [];
    foreach($list as $f)
    {
      $return[] = FilerObject::create($f);
    }
    return new Response($return);
    // if file, retrieve
    // if dir, glob
  }

  public function putUpload()
  {
    // save, if not dir
  }

  public function deleteUpload()
  {
    // remove, if not dir
  }

  /**
   * @return ConfigSectionInterface
   * @throws \Exception
   */
  private function _getConfig()
  {
    return $this->getContext()->config()->getSection('upload');
  }

  /**
   * @return FileStoreInterface
   * @throws \Exception
   */
  private function _getStore(): FileStoreInterface
  {
    $config = $this->_getConfig();
    $class = $config->getItem('filestore_class');
    /** @var FileStoreInterface $obj */
    $obj = new $class();
    $obj->configure($config);
    return $obj;
  }
}
