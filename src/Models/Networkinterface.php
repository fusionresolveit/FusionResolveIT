<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Networkinterface extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Networkinterface::class;
  protected $titles = ['Network interface', 'Network interfaces'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
