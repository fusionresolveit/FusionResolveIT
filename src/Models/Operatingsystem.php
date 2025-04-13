<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystem extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystem::class;
  protected $titles = ['Operating system', 'Operating systems'];
  protected $icon = 'operatingsystem';
  protected $hasEntityField = false;
}
