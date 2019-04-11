<?php
define('PHP_START', microtime(true));

use Cubex\Context\Context;
use Cubex\Cubex;
use Cubex\Quantum\Example\Project;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$isDev = true;
$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
$cubex = new Cubex(dirname(__DIR__), $loader);
//$launcher->listen(Cubex::EVENT_HANDLE_START, function (Context $ctx) { /* Configure your request here  */ });
try
{
  $project = new Project($cubex);
  $cubex->handle($project, true, !$cubex->getContext()->isEnv(Context::ENV_LOCAL));
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
