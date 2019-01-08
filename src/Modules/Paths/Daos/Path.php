<?php
namespace Cubex\Quantum\Modules\Paths\Daos;

use Cubex\Quantum\Base\Daos\QuantumQlDao;

class Path extends QuantumQlDao
{
  public $path;

  /**
   * @bool
   */
  public $published;

  public $handlerModule;
  public $handlerOptions;
}
