<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virtualmachinetype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Virtualmachinetype::class;
  protected $titles = ['Virtualization system', 'Virtualization systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
