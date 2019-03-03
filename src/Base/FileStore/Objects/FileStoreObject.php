<?php
namespace Cubex\Quantum\Base\FileStore\Objects;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;

class FileStoreObject implements FileStoreObjectInterface
{
  /**
   * @var string
   */
  protected $_path;
  /**
   * @var string
   */
  protected $_pseudoPath;

  public function __construct(string $path, string $basePath)
  {
    $this->_path = $path;
    $this->_pseudoPath = preg_replace('~^' . preg_quote($basePath, '~') . '~', '', $path);
  }

  public function getPath(): string
  {
    return $this->_pseudoPath;
  }

  public function getExtension(): string
  {
    return substr($this->_path, strrpos($this->_path, '.'));
  }

  public function getFileSize(): int
  {
    if($this->isFile())
    {
      return filesize($this->_path) ?: 0;
    }
    return 0;
  }

  public function isDir(): bool
  {
    return is_dir($this->_path);
  }

  public function isFile(): bool
  {
    return is_file($this->_path);
  }

  public function isLink(): bool
  {
    return is_link($this->_path);
  }

  public function getlinkTarget(): string
  {
    return $this->isLink() ? readlink($this->_path) : $this->_path;
  }
}
