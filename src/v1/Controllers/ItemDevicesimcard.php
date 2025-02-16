<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowItem;
use App\Traits\Subs\Contract;
use App\Traits\Subs\History;
use App\Traits\Subs\Infocom;

final class ItemDevicesimcard extends Common
{
  // Display
  use ShowItem;

  // Sub
  use History;

  protected $model = \App\Models\ItemDevicesimcard::class;
  protected $rootUrl2 = '/itemdevicesimcards/';

  protected function instanciateModel(): \App\Models\ItemDevicesimcard
  {
    return new \App\Models\ItemDevicesimcard();
  }
}
