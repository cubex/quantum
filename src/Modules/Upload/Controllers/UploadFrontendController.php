<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\FileStore\DiskFileStore;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Packaged\Config\ConfigSectionInterface;
use Packaged\Helpers\Path;
use Packaged\Http\Response;

class UploadFrontendController extends QuantumBaseController
{
  protected function _generateRoutes()
  {
    yield self::_route('{path@all}', 'retrieve');
  }

  public function getRetrieve()
  {
    $store = $this->_getStore();
    $path = $this->getContext()->routeData()->get('path', '');

    $fileObject = $store->getObject($path);
    return Response::create($fileObject->getContents(), 200, ['Content-type' => $fileObject->getMimeType()]);
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

    if($obj instanceof DiskFileStore && !$config->has('base_path'))
    {
      $basePath = Path::system($this->getContext()->getProjectRoot(), '.upload');
      $config->addItem('base_path', $basePath);
      mkdir($basePath, 0777, true);
    }
    $obj->configure($config);
    return $obj;
  }
}
