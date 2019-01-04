<?php
namespace Cubex\Quantum\Services\Paths\Daos;

use Cubex\Quantum\Base\Daos\QuantumQlDao;

class Page extends QuantumQlDao
{
  public $path;

  /**
   * @bool
   */
  public $published;

  public $handler;
  public $handlerOptions;
}
