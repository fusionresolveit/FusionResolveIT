<?php

declare(strict_types=1);

namespace App;

use Slim\Middleware\Session;

class SessionHandler extends Session
{
  public function forceStartSession(): void
  {
    $this->StartSession();
  }
}
