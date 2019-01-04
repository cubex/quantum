<?php
namespace Cubex\Quantum\Base\Daos;

use Packaged\Dal\Exceptions\Dao\DaoException;
use Packaged\Dal\Ql\QlDao;

abstract class QuantumQlDao extends QlDao
{
  protected $_dataStoreName = 'quantum_sql';

  public $siteId;

  public $id;

  public $createdTime;
  public $updatedTime;

  protected function _construct()
  {
    $this->createdTime = $this->updatedTime = time();
  }

  public function getDaoIDProperties()
  {
    return ['siteId', 'id'];
  }

  public function save()
  {
    if(empty($this->siteId))
    {
      throw new DaoException('Data requires a site id');
    }

    if($this->hasChanges())
    {
      $this->updatedTime = time();
    }
    parent::save();
  }
}
