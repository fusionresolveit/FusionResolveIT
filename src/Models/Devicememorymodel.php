<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicememorymodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicememorymodel::class;
  protected $titles = ['Device memory model', 'Device memory models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
