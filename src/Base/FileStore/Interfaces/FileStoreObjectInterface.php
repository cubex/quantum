<?php
namespace Cubex\Quantum\Base\FileStore\Interfaces;

interface FileStoreObjectInterface
{
  public function getPath(): string;

  public function getUrl(): string;

  public function getExtension(): string;

  public function getFileSize(): int;

  /**
   * @return bool
   */
  public function isDir(): bool;

  public function getMimeType(): string;

  public function getContents(): string;
}
