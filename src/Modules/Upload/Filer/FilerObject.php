<?php
namespace Cubex\Quantum\Modules\Upload\Filer;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use JsonSerializable;

class FilerObject implements JsonSerializable
{
  private $_path;
  private $_name;
  private $_type;

  private function __construct() { }

  public static function create(FileStoreObjectInterface $fileStoreObject)
  {
    $o = new static;
    $o->_path = $fileStoreObject->getPath();
    $o->_name = basename($fileStoreObject->getPath());
    $o->_type = $fileStoreObject->isDir() ? 'dir' : 'file';
    return $o;
  }

  public function jsonSerialize()
  {
    return [
      'path' => $this->_path,
      'name' => $this->_name,
      'type' => $this->_type,
    ];
  }
}
