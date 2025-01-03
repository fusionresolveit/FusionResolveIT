<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load lang
$lang = new \App\Translation();
$translator = $lang->loadLanguage();
$apiversion = 'v1';
$basePath = "/";
$phpunit = false;

// manage the basepath dynamically
if (isset($_SERVER['REQUEST_URI']) && strstr($_SERVER['REQUEST_URI'], 'index.php'))
{
  $uri_spl = explode('index.php', $_SERVER['REQUEST_URI']);
  $basePath = $uri_spl[0] . "index.php";
}
elseif (isset($_SERVER['REQUEST_URI']) && strstr($_SERVER['REQUEST_URI'], '/'))
{
  $uri_spl = explode('/', $_SERVER['REQUEST_URI']);
  $paths = [];
  foreach ($uri_spl as $path)
  {
    if ($path == '')
    {
      continue;
    }
    if (in_array($path, ['ping', 'view', 'api']))
    {
      break;
    } else {
      $paths[] = $path;
    }
  }
  $basePath = '/' . implode('/', $paths);
}

// Run app
$app = (new App\App())->get();
$app->run();
