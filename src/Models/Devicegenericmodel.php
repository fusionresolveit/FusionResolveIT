<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegenericmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicegenericmodel::class;
  protected $titles = ['Device generic model', 'Device generic models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
