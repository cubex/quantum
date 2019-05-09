<?php
namespace Cubex\Quantum\Base\FileStore\Interfaces;

interface FileStoreObjectInterface
{
  /**
   * @return string
   */
  public function getPath(): string;

  /**
   * @return string
   */
  public function getUrl(): string;

  /**
   * @return string
   */
  public function getExtension(): string;

  /**
   * @return int
   */
  public function getFileSize(): int;

  /**
   * @return bool
   */
  public function isDir(): bool;

  /**
   * @return string
   */
  public function getMimeType(): string;

  /**
   * @return string
   */
  public function getContents(): string;
}
