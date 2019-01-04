<?php
namespace Cubex\Quantum\Base\Interfaces;

use Cubex\Quantum\Base\QuantumProject;

interface QuantumAware
{
  public function setQuantum(QuantumProject $project);

  public function getQuantum(): QuantumProject;
}
