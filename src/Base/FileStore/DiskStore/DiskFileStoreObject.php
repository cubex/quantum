<?php
namespace Cubex\Quantum\Base\FileStore\DiskStore;

use Cubex\Quantum\Base\FileStore\Interfaces\FileStoreObjectInterface;
use Packaged\Helpers\Path;

class DiskFileStoreObject implements FileStoreObjectInterface
{
  /**
   * @var string
   */
  protected $_relativePath;
  /**
   * @var string
   */
  protected $_localBasePath;
  /**
   * @var string
   */
  protected $_urlBasePath;

  public function __construct(string $relativePath, string $localBasePath, string $urlBasePath)
  {
    $this->_relativePath = $relativePath;
    $this->_localBasePath = $localBasePath;
    $this->_urlBasePath = $urlBasePath;
  }

  public function getPath(): string
  {
    return $this->_relativePath;
  }

  public function getUrl(): string
  {
    return Path::url($this->_urlBasePath, $this->_relativePath);
  }

  public function getExtension(): string
  {
    return substr($this->_relativePath, strrpos($this->_relativePath, '.'));
  }

  public function getFileSize(): int
  {
    if($this->isFile())
    {
      return filesize($this->_getLocalPath()) ?: 0;
    }
    return 0;
  }

  public function isDir(): bool
  {
    return is_dir($this->_getLocalPath());
  }

  public function getMimeType(): string
  {
    $filePath = $this->_getLocalPath();
    if(function_exists('finfo_open'))
    {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_file($finfo, $filePath);
      finfo_close($finfo);
    }
    else
    {
      $mimeType = mime_content_type($filePath);
    }
    return $mimeType ?: 'application/octet-stream';
  }

  public function getContents(): string
  {
    return file_get_contents($this->_getLocalPath());
  }

  /**
   * @return string
   */
  private function _getLocalPath(): string
  {
    return Path::system($this->_localBasePath, $this->_relativePath);
  }
}
