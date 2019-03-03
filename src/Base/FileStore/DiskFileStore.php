<?php
namespace Cubex\Quantum\Base\FileStore;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Cubex\Quantum\Base\FileStore\Objects\FileStoreObject;
use DirectoryIterator;
use Exception;
use Packaged\Config\ConfigSectionInterface;
use Packaged\Helpers\Path;

class DiskFileStore implements FileStoreInterface
{
  /**
   * @var ConfigSectionInterface
   */
  private $_config;

  public function configure(ConfigSectionInterface $configuration)
  {
    $this->_config = $configuration;
  }

  /**
   * @param $path
   *
   * @return FileStoreObjectInterface[]
   * @throws FileStoreException
   */
  public function list($path): array
  {
    $objects = [];
    try
    {
      $iterator = new DirectoryIterator($this->_getFullPath($path));
    }
    catch(Exception $e)
    {
      throw new FileStoreException('specified path is not a valid directory');
    }
    foreach($iterator as $file)
    {
      $base = $file->getBasename();
      if($base !== '.' && $base !== '..')
      {
        $objects[] = $this->_getFileObject($file->getPathname());
      }
    }
    return $objects;
  }

  public function store($path, $data, $metadata): bool
  {
    $result = file_put_contents($this->_getFullPath($path), $data);
    return $result !== false ? true : false;
  }

  public function delete($path): bool
  {
    return unlink($this->_getFullPath($path));
  }

  /**
   * @param $path
   *
   * @return string
   * @throws FileStoreException
   */
  public function retrieve($path): string
  {
    $path = $this->_getFullPath($path);
    if(file_exists($path))
    {
      $contents = file_get_contents($path);
      if($contents !== false)
      {
        return $contents;
      }
    }
    throw new FileStoreException('file not found', 404);
  }

  public function move($fromPath, $toPath): bool
  {
    $fromPath = $this->_getFullPath($fromPath);
    $toPath = $this->_getFullPath($toPath);
    return rename($fromPath, $toPath);
  }

  public function copy($fromPath, $toPath): bool
  {
    $fromPath = $this->_getFullPath($fromPath);
    $toPath = $this->_getFullPath($toPath);
    return copy($fromPath, $toPath);
  }

  private function _getFullPath($path)
  {
    return Path::system($this->_getBasePath(), $path);
  }

  private function _getBasePath()
  {
    return $this->_config->getItem('base_path', '/');
  }

  private function _getFileObject($fullPath)
  {
    return new FileStoreObject($fullPath, $this->_getBasePath());
  }
}
