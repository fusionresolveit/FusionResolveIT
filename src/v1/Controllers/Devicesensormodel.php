<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowAll;
use App\Traits\ShowItem;
use App\Traits\Subs\History;

final class Devicesensormodel extends Common
{
  // Display
  use ShowItem;
  use ShowAll;

  // Sub
  use History;

  protected $model = \App\Models\Devicesensormodel::class;

  protected function instanciateModel(): \App\Models\Devicesensormodel
  {
    return new \App\Models\Devicesensormodel();
  }
}
