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

  private $_resolvedBasePath;

  /**
   * @param ConfigSectionInterface $configuration
   *
   * @return $this
   * @throws Exception
   */
  public function configure(ConfigSectionInterface $configuration)
  {
    $this->_config = $configuration;
    $basePath = $this->_config->getItem('upload_dir');
    if(substr($basePath, 0, 1) !== '/')
    {
      $basePath = Path::system($this->_config->getItem('project_root'), $basePath);
    }
    if($basePath && !file_exists($basePath))
    {
      mkdir($basePath);
    }
    $this->_resolvedBasePath = $basePath;
    return $this;
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
        $path = $file->getPathname();
        $relPath = preg_replace('~^' . preg_quote($this->_resolvedBasePath, '~') . '~', '', $path);
        $objects[] = $this->_getFileObject($relPath);
      }
    }
    return $objects;
  }

  public function store($path, $data, $metadata): bool
  {
    $result = file_put_contents($this->_getFullPath($path), $data);
    return $result !== false ? true : false;
  }

  public function mkdir($path): bool
  {
    return mkdir($path);
  }

  public function delete($path): bool
  {
    $fullPath = $this->_getFullPath($path);
    if(is_dir($fullPath))
    {
      return rmdir($fullPath);
    }
    return unlink($fullPath);
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

  public function rename($fromPath, $toPath): bool
  {
    $fromPath = $this->_getFullPath($fromPath);
    $toPath = $this->_getFullPath($toPath);
    if((!file_exists($fromPath)) || file_exists($toPath))
    {
      return false;
    }
    return rename($fromPath, $toPath);
  }

  public function copy($fromPath, $toPath): bool
  {
    $fromPath = $this->_getFullPath($fromPath);
    $toPath = $this->_getFullPath($toPath);
    return copy($fromPath, $toPath);
  }

  public function getObject($relativePath): FileStoreObjectInterface
  {
    return $this->_getFileObject($relativePath);
  }

  private function _getFullPath($path)
  {
    return Path::system($this->_resolvedBasePath, $path);
  }

  private function _getUrlBasePath()
  {
    return $this->_config->getItem('url_root', '/');
  }

  private function _getFileObject($relativePath)
  {
    return new FileStoreObject($relativePath, $this->_resolvedBasePath, $this->_getUrlBasePath());
  }
}
