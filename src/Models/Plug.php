<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plug extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Plug::class;
  protected $titles = ['Plug', 'Plugs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
