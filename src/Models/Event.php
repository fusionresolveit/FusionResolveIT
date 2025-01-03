<?php

declare(strict_types=1);

namespace App\Models;

class Event extends Common
{
  protected $definition = '\App\Models\Definitions\Event';
  protected $titles = ['Log', 'Logs'];
  protected $icon = 'scroll';
  protected $hasEntityField = false;
}
