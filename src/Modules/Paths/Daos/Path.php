<?php
namespace Cubex\Quantum\Modules\Paths\Daos;

use Cubex\Quantum\Base\Daos\QuantumQlDao;
use Symfony\Component\HttpFoundation\ParameterBag;

class Path extends QuantumQlDao
{
  public $path;

  public $handlerModule;
  public $handlerOptions;

  protected function _configure()
  {
    parent::_configure();
    $this->_addCustomSerializer(
      'handlerOptions',
      '',
      function (ParameterBag $param = null) {
        return $param ? serialize($param->all()) : null;
      },
      function ($options) {
        return new ParameterBag(unserialize($options));
      }
    );
  }
}
