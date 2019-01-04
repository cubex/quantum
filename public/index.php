<?php
define('PHP_START', microtime(true));

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$isDev = true;
$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
$launcher = new \Cubex\Cubex(dirname(__DIR__), $loader);
//$launcher->listen(Cubex::EVENT_HANDLE_START, function (Context $ctx) { /* Configure your request here  */ });
try
{
  (new \Cubex\Quantum\Project($launcher))->handle(true, !$isDev);
}
catch(Throwable $e)
{
  if(!$isDev)
  {
    die('Unable to handle your request');
  }

  $handler = new Run();
  $handler->pushHandler(new PrettyPageHandler());
  $handler->handleException($e);
}
