<?php
define('PHP_START', microtime(true));

use Cubex\Cubex;
use Cubex\Quantum\Example\Project;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
$cubex = new Cubex(dirname(__DIR__), $loader);
try
{
  $project = new Project($cubex);
  $cubex->handle($project, true);
}
catch(Throwable $e)
{
  $handler = new Run();
  $handler->pushHandler(new PrettyPageHandler());
  $handler->handleException($e);
}
