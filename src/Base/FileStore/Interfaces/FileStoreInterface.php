<?php
namespace Cubex\Quantum\Base\FileStore\Interfaces;

use Cubex\Quantum\Base\FileStore\FileStoreException;
use Packaged\Config\ConfigurableInterface;

interface FileStoreInterface extends ConfigurableInterface
{
  /**
   * @param $path
   *
   * @return FileStoreObjectInterface[]
   * @throws FileStoreException
   */
  public function list($path): array;

  public function store($path, $data, $metadata): bool;

  public function mkdir($path): bool;

  public function delete($path): bool;

  public function retrieve($path): string;

  public function rename($fromPath, $toPath): bool;

  public function copy($fromPath, $toPath): bool;

  public function getObject($relativePath): FileStoreObjectInterface;
}
