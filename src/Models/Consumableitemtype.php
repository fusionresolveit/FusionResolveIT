<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumableitemtype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Consumableitemtype::class;
  protected $titles = ['Consumable type', 'Consumable types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
