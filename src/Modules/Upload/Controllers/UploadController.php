<?php
namespace Cubex\Quantum\Modules\Upload\Controllers;

use Cubex\Quantum\Base\Controllers\QuantumAdminController;
use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Cubex\Quantum\Modules\Upload\Filer\FilerObject;
use Packaged\Routing\RequestDataConstraint;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Div;
use Packaged\Helpers\Objects;
use Packaged\Helpers\Path;
use Packaged\Helpers\ValueAs;
use Packaged\Http\Response;
use Packaged\Http\Responses\JsonResponse;
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
    $success = $this->_getStore()->rename($this->request()->request->get('from'), $this->request()->request->get('to'));
    return JsonResponse::create($success ? true : 'unable to rename');
  }

  public function postUpload()
  {
    $name = basename($_FILES['file']['name']);
    $success = $this->_getStore()->store(
      Path::system($this->request()->request->get('path'), $name),
      file_get_contents($_FILES['file']['tmp_name']),
      []
    );
    return JsonResponse::create($success ? true : 'unable to upload');
  }

  public function postDelete()
  {
    $success = $this->_getStore()->delete($this->request()->request->get('path'));
    return JsonResponse::create($success ? true : 'unable to delete');
  }

  public function postConnector()
  {
    $store = $this->_getStore();
    $path = ValueAs::nonempty($this->request()->request->get('path'), '/');

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
}
