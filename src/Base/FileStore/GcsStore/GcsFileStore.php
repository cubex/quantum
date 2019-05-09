<?php
namespace Cubex\Quantum\Base\FileStore\GcsStore;

use Cubex\Quantum\Base\FileStore\FileStoreException;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreInterface;
use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Exception;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Packaged\Config\ConfigSectionInterface;
use Packaged\Helpers\Path;

class GcsFileStore implements FileStoreInterface
{
  /**
   * @var ConfigSectionInterface
   */
  private $_config;

  private $_resolvedBasePath;

  private $_client;

  protected function _getClient()
  {
    if(!$this->_client)
    {
      $this->_client = new StorageClient(
        [
          'projectId'   => $this->_config->getItem('project_id'),
          'keyFilePath' => $this->_config->getItem('key_file_path'),
        ]
      );
    }
    return $this->_client;
  }

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
    $this->_resolvedBasePath = Path::url($basePath, '/');
    return $this;
  }

  /**
   * @param $relativePath
   *
   * @return FileStoreObjectInterface[]
   * @throws Exception
   */
  public function list(string $relativePath): array
  {
    $bucket = $this->_getBucket();
    $bucketPath = $this->_getFullPath($relativePath);
    $files = $bucket->objects(['prefix' => $bucketPath, 'delimiter' => '/']);

    $objects = [];
    /** @var StorageObject $file */
    foreach($files as $file)
    {
      $name = $file->name();
      if($name !== $bucketPath)
      {
        $relPath = preg_replace('~^' . preg_quote($this->_resolvedBasePath, '~') . '~', '', $name);
        $objects[] = $this->_getFileObject($relPath);
      }
    }
    foreach($files->prefixes() as $name)
    {
      $relPath = preg_replace('~^' . preg_quote($this->_resolvedBasePath, '~') . '~', '', $name);
      $objects[] = $this->_getFileObject($relPath);
    }
    return $objects;
  }

  public function store(string $relativePath, $data, array $metadata): bool
  {
    $result = $this->_getBucket()->upload($data, ['name' => $this->_getFullPath($relativePath)]);
    return $result->exists() !== false ? true : false;
  }

  public function mkdir(string $relativePath): bool
  {
    $result = $this->_getBucket()->upload('', ['name' => $this->_getFullPath($relativePath) . '/']);
    return $result->exists() !== false ? true : false;
  }

  public function delete(string $relativePath): bool
  {
    $this->_getBucket()->object($this->_getFullPath($relativePath))->delete();
    return true;
  }

  /**
   * @param string $relativePath
   *
   * @return string
   * @throws FileStoreException
   */
  public function retrieve(string $relativePath): string
  {
    $obj = $this->_getBucketObject($relativePath);
    if($obj->exists())
    {
      return $obj->downloadAsString();
    }
    throw new FileStoreException('file not found', 404);
  }

  public function rename(string $fromRelativePath, string $toRelativePath): bool
  {
    $storage = $this->_getBucketObject($fromRelativePath)->rename($this->_getFullPath($toRelativePath));
    return $storage->exists();
  }

  public function copy(string $fromRelativePath, string $toRelativePath): bool
  {
    $storage = $this->_getBucketObject($fromRelativePath)->copy($this->_getFullPath($toRelativePath));
    return $storage->exists();
  }

  public function getObject(string $relativePath): FileStoreObjectInterface
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
    $object = $this->_getBucketObject($relativePath);
    $info = $object->info();
    $isDir = substr($object->name(), -1) === '/';
    return new GcsFileStoreObject(
      $this,
      $relativePath,
      $this->_getUrlBasePath(),
      $isDir,
      $isDir ? 0 : $info['size'],
      $isDir ? '' : $info['contentType']
    );
  }

  /**
   * @return Bucket
   */
  private function _getBucket()
  {
    return $bucket = $this->_getClient()->bucket($this->_config->getItem('bucket'));
  }

  /**
   * @param $relativePath
   *
   * @return StorageObject
   */
  private function _getBucketObject($relativePath)
  {
    $bucket = $this->_getBucket();
    $bucketPath = $this->_getFullPath($relativePath);
    return $bucket->object($bucketPath);
  }
}
