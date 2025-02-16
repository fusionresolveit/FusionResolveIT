<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemservicepack extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemservicepack::class;
  protected $titles = ['Service pack', 'Service packs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
