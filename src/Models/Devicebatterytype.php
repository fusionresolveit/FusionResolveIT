<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicebatterytype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicebatterytype::class;
  protected $titles = ['Battery type', 'Battery types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
