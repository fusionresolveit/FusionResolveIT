<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memorytype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Memorytype::class;
  protected $titles = ['Memory type', 'Memory types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
