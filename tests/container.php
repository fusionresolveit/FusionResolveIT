<?php

use Psr\Container\ContainerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;

return [
  BasePathMiddleware::class => function (ContainerInterface $container) {
    return new BasePathMiddleware($container->get(App::class));
  },
];
