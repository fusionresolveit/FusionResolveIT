<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitortype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Monitortype::class;
  protected $titles = ['Monitor type', 'Monitor types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
