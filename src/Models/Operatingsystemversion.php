<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemversion extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemversion::class;
  protected $titles = ['Version of the operating system', 'Versions of the operating systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $casts = [
    'is_lts' => 'boolean',
  ];
}
