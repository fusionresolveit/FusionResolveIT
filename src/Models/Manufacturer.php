<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Manufacturer::class;
  protected $titles = ['Manufacturer', 'Manufacturers'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
