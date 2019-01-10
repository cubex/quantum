<?php
namespace Cubex\Quantum\Modules\Pages\Daos;

use Cubex\Quantum\Base\Daos\QuantumQlDao;

class Page extends QuantumQlDao
{
  public $path;

  public $title;
  public $content;

  public $theme;
}
