<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interfacetype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Interfacetype::class;
  protected $titles = ['Interface type (Hard drive...)', 'Interface types (Hard drive...)'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
