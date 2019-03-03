<?php
namespace Cubex\Quantum\Base\FileStore\Interfaces;

interface FileStoreObjectInterface
{
  public function getPath(): string;

  public function getExtension(): string;

  public function getFileSize(): int;

  /**
   * @return bool
   */
  public function isDir(): bool;

  /**
   * @return bool
   */
  public function isFile(): bool;

  /**
   * @return bool
   */
  public function isLink(): bool;

  /**
   * @return string
   */
  public function getlinkTarget(): string;
}
