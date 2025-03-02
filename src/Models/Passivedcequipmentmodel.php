<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passivedcequipmentmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Passivedcequipmentmodel::class;
  protected $titles = ['Passive device model', 'Passive device models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
