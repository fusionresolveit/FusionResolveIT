<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesensortype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesensortype::class;
  protected $titles = ['Sensor type', 'Sensor types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
