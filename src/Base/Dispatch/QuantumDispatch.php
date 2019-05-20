<?php
namespace Cubex\Quantum\Base\Dispatch;

use Packaged\Context\Context;
use Packaged\Dispatch\Dispatch;
use Packaged\Routing\Handler\Handler;
use Symfony\Component\HttpFoundation\Response;

class QuantumDispatch extends Dispatch implements Handler
{
  const PATH = '/_r';

  public function __construct($projectRoot, $baseUri = null)
  {
    parent::__construct($projectRoot, $baseUri);
    $this->addComponentAlias('\Cubex\Quantum', 'quantum');
  }

  /**
   * @param Context $c
   *
   * @return Response
   * @throws \Exception
   */
  public function handle(Context $c): Response
  {
    return $this->handleRequest($c->request());
  }

}
