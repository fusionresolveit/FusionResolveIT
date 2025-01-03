<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Consumableitemtype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Consumableitemtype';
  protected $titles = ['Consumable type', 'Consumable types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
