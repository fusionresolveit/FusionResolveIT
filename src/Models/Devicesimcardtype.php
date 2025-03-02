<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesimcardtype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesimcardtype::class;
  protected $titles = ['Simcard type', 'Simcard types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
