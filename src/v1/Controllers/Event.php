<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowAll;
use App\Traits\ShowItem;

final class Event extends Common
{
  // Display
  use ShowItem;
  use ShowAll;

  protected $model = \App\Models\Event::class;

  protected function instanciateModel(): \App\Models\Event
  {
    return new \App\Models\Event();
  }
}
