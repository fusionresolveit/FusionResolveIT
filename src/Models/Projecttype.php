<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projecttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Projecttype::class;
  protected $titles = ['Project type', 'Project types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
