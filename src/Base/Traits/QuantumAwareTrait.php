<?php
namespace Cubex\Quantum\Base\Traits;

use Cubex\Quantum\Base\QuantumProject;

trait QuantumAwareTrait
{
  protected $_quantum;

  public function setQuantum(QuantumProject $project)
  {
    $this->_quantum = $project;
    return $this;
  }

  public function getQuantum(): QuantumProject
  {
    return $this->_quantum;
  }
}
