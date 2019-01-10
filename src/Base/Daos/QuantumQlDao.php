<?php
namespace Cubex\Quantum\Base\Daos;

use Packaged\Dal\Ql\QlDao;

abstract class QuantumQlDao extends QlDao
{
  protected $_dataStoreName = 'quantum_sql';

  public $id;

  public $createdTime;
  public $updatedTime;

  protected function _construct()
  {
    $this->createdTime = $this->updatedTime = time();
  }

  public function getDaoIDProperties()
  {
    return ['id'];
  }

  public function save()
  {
    if($this->hasChanges())
    {
      $this->createdTime = $this->updatedTime = time();
    }
    return parent::save();
  }
}
