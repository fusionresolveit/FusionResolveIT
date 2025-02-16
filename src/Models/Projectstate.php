<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projectstate extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Projectstate::class;
  protected $titles = ['Project state', 'Project states'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
