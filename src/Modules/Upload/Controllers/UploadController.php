<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Base\FileStore\DiskFileStore;
use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Cubex\Quantum\Modules\Upload\Filer\FilerObject;
use Packaged\Config\ConfigSectionInterface;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Path;
use Packaged\Http\Response;
use Packaged\Http\Responses\JsonResponse;

class UploadController extends QuantumAdminController implements DispatchableComponent
{
  protected function _generateRoutes()
  {
    // these routes fail because the path is matched and routedPath is updated, but the other conditions fail
    //yield self::_route('connector', 'upload')->add(RequestDataContraint::i()->post('action', 'upload'));
    //yield self::_route('connector', 'delete')->add(RequestDataContraint::i()->post('action', 'delete'));
    yield self::_route('connector', 'connector');
    yield self::_route('', 'page');
  }

  public function getPage()
  {
    ResourceManager::component($this)->requireJs('filer.min.js');
    return Div::create()->setId('filer-container');
  }

  public function postUpload()
  {
    $name = basename($_FILES['file']['name']);
    $this->_getStore()->store(
      Path::system($this->request()->request->get('path'), $name),
      file_get_contents($_FILES['file']['tmp_name']),
      []
    );
    return 'success';
  }

  public function postDelete()
  {
    $this->_getStore()->delete($this->request()->request->get('path'));
    return 'success';
  }

  public function postConnector()
  {
    switch($this->request()->request->get('action'))
    {
      case 'upload':
        return $this->postUpload();
      case 'delete':
        return $this->postDelete();
    }

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
    $return[] = [
      'path' => '!trash!',
      'name' => '',
      'type' => 'trash',
    ];
    foreach($list as $f)
    {
      $return[] = FilerObject::create($f);
    }
    return JsonResponse::create($return);
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
