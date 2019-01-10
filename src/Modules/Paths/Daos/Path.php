<?php
namespace Cubex\Quantum\Modules\Paths\Daos;

use Packaged\Dal\Ql\QlDao;
use Symfony\Component\HttpFoundation\ParameterBag;

class Path extends QlDao
{
  protected $_dataStoreName = 'quantum_sql';

  public $path;

  public $handlerModule;
  public $handlerOptions;

  public function getDaoIDProperties()
  {
    return ['path'];
  }

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
