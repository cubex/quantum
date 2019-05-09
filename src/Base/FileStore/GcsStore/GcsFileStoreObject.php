<?php

namespace Cubex\Quantum\Base\FileStore\GcsStore;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Packaged\Helpers\Path;

class GcsFileStoreObject implements FileStoreObjectInterface
{
  protected $_store;
  protected $_relativePath;
  protected $_publicBase;
  protected $_size;
  protected $_isDir;
  protected $_mimeType;

  public function __construct(GcsFileStore $store, $relPath, $publicBase, $isDir, $size, $mimeType)
  {
    $this->_store = $store;
    $this->_relativePath = $relPath;
    $this->_publicBase = $publicBase;
    $this->_isDir = $isDir;
    $this->_size = $size;
    $this->_mimeType = $mimeType;
  }

  public function getPath(): string
  {
    return $this->_relativePath;
  }

  public function getUrl(): string
  {
    return Path::url($this->_publicBase, $this->_relativePath);
  }

  public function getExtension(): string
  {
    return substr($this->_relativePath, strrpos($this->_relativePath, '.'));
  }

  public function getFileSize(): int
  {
    return $this->_size;
  }

  public function isDir(): bool
  {
    return $this->_isDir;
  }

  public function getMimeType(): string
  {
    return $this->_mimeType;
  }

  public function getContents(): string
  {
    return $this->_store->retrieve($this->_relativePath);
  }
}
