<?php
namespace Cubex\Quantum\Base\Uri;

use Cubex\Context\Context;
use Cubex\Routing\Condition;
use Cubex\Routing\RequestConstraint;

class Uri implements Condition
{
  /**
   * @var string
   */
  protected $_uri;

  protected function __construct()
  {
  }

  /**
   * @param string $uri
   *
   * @return Uri
   */
  public static function create($uri)
  {
    $o = new static;
    $o->_uri = $uri;
    return $o;
  }

  protected function _getConstraint()
  {
    $c = RequestConstraint::i();

    $parts = parse_url($this->_uri);
    if(isset($parts['scheme']))
    {
      $c->scheme($parts['scheme']);
    }
    if(isset($parts['host']))
    {
      $c->hostname($parts['host']);
    }
    if(isset($parts['port']))
    {
      $c->port($parts['port']);
    }
    if(isset($parts['path']))
    {
      $c->path($parts['path']);
    }
    if(isset($parts['query']))
    {
      $queryParts = explode('&', $parts['query']);
      foreach($queryParts as $part)
      {
        [$k, $v] = $part;
        $c->hasQueryValue($k, $v);
      }
    }

    return $c;
  }

  public function match(Context $context): bool
  {
    return $this->_getConstraint()->match($context);
  }

  public function __toString()
  {
    return $this->_uri;
  }
}
