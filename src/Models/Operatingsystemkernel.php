<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemkernel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemkernel::class;
  protected $titles = ['Kernel', 'Kernels'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
