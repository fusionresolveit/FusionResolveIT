<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;

final class Devicegenericmodel extends Common
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicegenericmodel::class;

  protected function instanciateModel(): \App\Models\Devicegenericmodel
  {
    return new \App\Models\Devicegenericmodel();
  }
}
