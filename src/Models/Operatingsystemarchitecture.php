<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemarchitecture extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemarchitecture::class;
  protected $titles = ['Operating system architecture', 'Operating system architectures'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
