<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memorymodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Memorymodel::class;
  protected $titles = ['Memory model', 'Memory models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
