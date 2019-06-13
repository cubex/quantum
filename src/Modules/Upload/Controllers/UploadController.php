<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Cubex\Quantum\Modules\Upload\Filer\FilerObject;
use Packaged\Dal\Cache\CacheItem;
use Packaged\Dal\Cache\ICacheConnection;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Path;
use Packaged\Helpers\ValueAs;
use Packaged\Http\Response;
use Packaged\Http\Responses\JsonResponse;
use Packaged\Routing\RequestDataConstraint;
use PackagedUi\Fusion\Card\Card;

class UploadController extends QuantumAdminController implements DispatchableComponent
{
  protected function _generateRoutes()
  {
    // these routes fail because the path is matched and routedPath is updated, but the other conditions fail
    yield self::_route('connector', 'rename')->add(RequestDataConstraint::i()->post('action', 'rename'));
    yield self::_route('connector', 'upload')->add(RequestDataConstraint::i()->post('action', 'upload'));
    yield self::_route('connector', 'delete')->add(RequestDataConstraint::i()->post('action', 'delete'));
    yield self::_route('connector', 'connector');
    yield self::_route('', 'page');
  }

  public function getPage()
  {
    ResourceManager::component($this)->requireJs('filer.min.js');
    return Card::create()
      ->setHeader('Uploads')
      ->setContent(Div::create()->setId('filer-container'));
  }

  public function postRename()
  {
    $fromPath = $this->request()->request->get('from');
    $toPath = $this->request()->request->get('to');
    $success = $this->_getStore()->rename($fromPath, $toPath);
    if($success && ($cache = $this->_getCache()))
    {
      $cache->deleteItems(
        [
          $this->_getCacheKey(dirname($fromPath)),
          $this->_getCacheKey(dirname($toPath)),
        ]
      );
    }
    return JsonResponse::create($success ? true : 'unable to rename');
  }

  public function postUpload()
  {
    $path = ValueAs::nonempty($this->request()->request->get('path'), '/');
    $name = basename($_FILES['file']['name']);
    $success = $this->_getStore()->store(
      Path::unix($path, $name),
      file_get_contents($_FILES['file']['tmp_name']),
      []
    );
    if($success && ($cache = $this->_getCache()))
    {
      $cache->deleteKey($this->_getCacheKey($path));
    }
    return JsonResponse::create($success ? true : 'unable to upload');
  }

  public function postDelete()
  {
    $path = $this->request()->request->get('path');
    $dirPath = dirname($path);
    $success = $this->_getStore()->delete($path);
    if($success && ($cache = $this->_getCache()))
    {
      $cache->deleteKey($this->_getCacheKey($dirPath));
    }
    return JsonResponse::create($success ? true : 'unable to delete');
  }

  public function postConnector()
  {
    $store = $this->_getStore();
    $path = ValueAs::nonempty($this->request()->request->get('path'), '/');

    $cacheKey = $this->_getCacheKey($path);
    $cache = $this->_getCache();

    $pathObj = $store->getObject($path);
    $list = [];
    if($pathObj->isDir())
    {
      /** @var FileStoreObjectInterface[] $list */
      try
      {
        $list = null;
        if($cache)
        {
          $cacheItem = $cache->getItem($cacheKey);
          if($cacheItem->exists())
          {
            $list = $cacheItem->get();
          }
        }
        if($list === null)
        {
          $list = $store->list($path);
          $list = array_reverse(Objects::msort($list, 'isDir'));
          if($cache)
          {
            $cache->saveItem(new CacheItem($cacheKey, $list));
          }
        }
      }
      catch(FileStoreException $e)
      {
        return Response::create($e->getMessage(), 400);
      }
    }

    $return = [
      [ // trash
        'path' => '!trash!',
        'name' => '',
        'type' => 'trash',
      ],
    ];
    // up a level
    if($path !== '/')
    {
      $return[] = [
        'path' => dirname($path),
        'name' => '..',
        'type' => 'dir',
      ];
    }
    foreach($list as $f)
    {
      $return[] = FilerObject::create($f);
    }
    return JsonResponse::create($return);
  }

  /**
   * @return FileStoreInterface
   */
  private function _getStore(): FileStoreInterface
  {
    return $this->getContext()->getCubex()->retrieve('upload-' . FileStoreInterface::class);
  }

  private function _getCache(): ?ICacheConnection
  {
    try
    {
      $cache = $this->getQuantum()->getCubex()->retrieve('upload-' . ICacheConnection::class);
      $cache->connect();
    }
    catch(\Exception $e)
    {
      $cache = null;
    }
    return $cache;
  }

  /**
   * @param $path
   *
   * @return string
   */
  private function _getCacheKey($path): string
  {
    return get_class($this->getQuantum()) . '~upload-list#' . md5($path);
  }
}
