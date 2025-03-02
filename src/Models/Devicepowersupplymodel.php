<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicepowersupplymodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicepowersupplymodel::class;
  protected $titles = ['Device power supply model', 'Device power supply models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
