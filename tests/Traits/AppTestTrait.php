<?php

namespace Tests\Traits;

use Selective\TestTrait\Traits\ContainerTestTrait;
use Selective\TestTrait\Traits\MockTestTrait;
use Slim\App;
use UnexpectedValueException;

trait AppTestTrait
{
  use ContainerTestTrait;
  use MockTestTrait;

  protected App $app;

  protected function setUp(): void
  {
    $this->app = require __DIR__ . '/../bootstrap.php';

    $container = $this->app->getContainer();
    if ($container === null)
    {
      throw new UnexpectedValueException('Container must be initialized');
    }
    $this->setUpContainer($container);
  }
}
