<?php
namespace Cubex\Quantum\Base\FileStore\Interfaces;

use Cubex\Quantum\Base\FileStore\FileStoreException;
use Packaged\Config\ConfigurableInterface;

interface FileStoreInterface extends ConfigurableInterface
{
  /**
   * @param $relativePath
   *
   * @return FileStoreObjectInterface[]
   * @throws FileStoreException
   */
  public function list(string $relativePath): array;

  public function store(string $relativePath, $data, array $metadata): bool;

  public function mkdir(string $relativePath): bool;

  public function delete(string $relativePath): bool;

  public function retrieve(string $relativePath): string;

  public function rename(string $fromRelativePath, string $toRelativePath): bool;

  public function copy(string $fromRelativePath, string $toRelativePath): bool;

  public function getObject(string $relativePath): FileStoreObjectInterface;
}
