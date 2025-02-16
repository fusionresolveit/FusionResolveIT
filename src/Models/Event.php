<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Event::class;
  protected $titles = ['Log', 'Logs'];
  protected $icon = 'scroll';
  protected $hasEntityField = false;
}
