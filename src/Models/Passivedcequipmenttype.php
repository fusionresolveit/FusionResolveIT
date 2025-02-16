<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passivedcequipmenttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Passivedcequipmenttype::class;
  protected $titles = ['Passive device type', 'Passive device types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
