<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemkernel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemkernel';
  protected $titles = ['Kernel', 'Kernels'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
