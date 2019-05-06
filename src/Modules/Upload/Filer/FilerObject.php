<?php
namespace Cubex\Quantum\Modules\Upload\Filer;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use JsonSerializable;

class FilerObject implements JsonSerializable
{
  private $_path;
  private $_url;
  private $_type;
  private $_mime;

  private function __construct() { }

  public static function create(FileStoreObjectInterface $fileStoreObject)
  {
    $o = new static;
    $o->_path = $fileStoreObject->getPath();
    $o->_url = $fileStoreObject->getUrl();
    $o->_type = $fileStoreObject->isDir() ? 'dir' : 'file';
    $o->_mime = $fileStoreObject->getMimeType();
    return $o;
  }

  public function jsonSerialize()
  {
    return [
      'name' => basename($this->_path),
      'path' => $this->_path,
      'url'  => $this->_url,
      'type' => $this->_type,
      'mime' => $this->_mime,
    ];
  }
}
