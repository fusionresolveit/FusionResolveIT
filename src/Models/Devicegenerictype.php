<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegenerictype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicegenerictype::class;
  protected $titles = ['Generic type', 'Generic types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
