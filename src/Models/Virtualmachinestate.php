<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virtualmachinestate extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Virtualmachinestate::class;
  protected $titles = ['State of the virtual machine', 'States of the virtual machine'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
