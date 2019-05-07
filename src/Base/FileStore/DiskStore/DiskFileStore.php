<?php
namespace Cubex\Quantum\Base\FileStore\DiskStore;

use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Exception;
use FilesystemIterator;
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
    if(substr($basePath, 0, 1) !== '/' && empty(parse_url($basePath, PHP_URL_SCHEME)))
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
      $iterator = new FilesystemIterator($this->_getFullPath($path));
    }
    catch(Exception $e)
    {
      throw new FileStoreException('specified path is not a valid directory');
    }
    foreach($iterator as $file)
    {
      $path = $file->getPathname();
      $relPath = preg_replace('~^' . preg_quote($this->_resolvedBasePath, '~') . '~', '', $path);
      $objects[] = $this->_getFileObject($relPath);
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
    return rtrim(Path::system($this->_resolvedBasePath, $path), '/');
  }

  private function _getUrlBasePath()
  {
    return $this->_config->getItem('url_root', '/');
  }

  private function _getFileObject($relativePath)
  {
    return new DiskFileStoreObject($relativePath, $this->_resolvedBasePath, $this->_getUrlBasePath());
  }
}
