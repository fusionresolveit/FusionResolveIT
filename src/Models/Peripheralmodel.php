<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peripheralmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Peripheralmodel::class;
  protected $titles = ['Peripheral model', 'Peripheral models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
