<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumBaseController;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
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
   * @return FileStoreInterface
   * @throws \Exception
   */
  private function _getStore(): FileStoreInterface
  {
    return $this->getContext()->getCubex()->retrieve('upload-' . FileStoreInterface::class);
  }
}
