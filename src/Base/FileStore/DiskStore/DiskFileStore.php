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
   * @param $relativePath
   *
   * @return FileStoreObjectInterface[]
   * @throws FileStoreException
   */
  public function list(string $relativePath): array
  {
    $objects = [];
    try
    {
      $iterator = new FilesystemIterator($this->_getFullPath($relativePath));
    }
    catch(Exception $e)
    {
      throw new FileStoreException('specified path is not a valid directory');
    }
    foreach($iterator as $file)
    {
      $relativePath = $file->getPathname();
      $relPath = preg_replace('~^' . preg_quote($this->_resolvedBasePath, '~') . '~', '', $relativePath);
      $objects[] = $this->_getFileObject($relPath);
    }
    return $objects;
  }

  public function store(string $relativePath, $data, array $metadata): bool
  {
    $result = file_put_contents($this->_getFullPath($relativePath), $data);
    return $result !== false ? true : false;
  }

  public function mkdir(string $relativePath): bool
  {
    return mkdir($relativePath);
  }

  public function delete(string $relativePath): bool
  {
    $fullPath = $this->_getFullPath($relativePath);
    if(is_dir($fullPath))
    {
      return rmdir($fullPath);
    }
    return unlink($fullPath);
  }

  /**
   * @param string $relativePath
   *
   * @return string
   * @throws FileStoreException
   */
  public function retrieve(string $relativePath): string
  {
    $relativePath = $this->_getFullPath($relativePath);
    if(file_exists($relativePath))
    {
      $contents = file_get_contents($relativePath);
      if($contents !== false)
      {
        return $contents;
      }
    }
    throw new FileStoreException('file not found', 404);
  }

  public function rename(string $fromRelativePath, string $toRelativePath): bool
  {
    $fromRelativePath = $this->_getFullPath($fromRelativePath);
    $toRelativePath = $this->_getFullPath($toRelativePath);
    if((!file_exists($fromRelativePath)) || file_exists($toRelativePath))
    {
      return false;
    }
    return rename($fromRelativePath, $toRelativePath);
  }

  public function copy(string $fromRelativePath, string $toRelativePath): bool
  {
    $fromRelativePath = $this->_getFullPath($fromRelativePath);
    $toRelativePath = $this->_getFullPath($toRelativePath);
    return copy($fromRelativePath, $toRelativePath);
  }

  public function getObject(string $relativePath): FileStoreObjectInterface
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
