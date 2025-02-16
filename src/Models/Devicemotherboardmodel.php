<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicemotherboardmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicemotherboardmodel::class;
  protected $titles = ['System board model', 'System board models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
