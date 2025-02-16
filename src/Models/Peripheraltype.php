<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peripheraltype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Peripheraltype::class;
  protected $titles = ['Peripheral type', 'Peripheral types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
