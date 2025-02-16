<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virtualmachinesystem extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Virtualmachinesystem::class;
  protected $titles = ['Virtualization model', 'Virtualization models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
