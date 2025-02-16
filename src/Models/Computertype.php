<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computertype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Computertype::class;
  protected $titles = ['Computer type', 'Computer types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
