<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfErrorHandler
{
  public static function handleFailure(ServerRequestInterface $request, RequestHandlerInterface $handler): void
  {
    throw new \Exception('Security: failed CSRF check', 403);
  }
}
