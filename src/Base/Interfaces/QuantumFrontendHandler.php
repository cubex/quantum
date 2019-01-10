<?php
namespace Cubex\Quantum\Base\Interfaces;

use Symfony\Component\HttpFoundation\ParameterBag;

interface QuantumFrontendHandler
{
  public function setOptions(ParameterBag $options);
}
