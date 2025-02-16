<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicefirmwaretype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicefirmwaretype::class;
  protected $titles = ['Firmware type', 'Firmware types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
