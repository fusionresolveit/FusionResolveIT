<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemedition extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemedition::class;
  protected $titles = ['Edition', 'Editions'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
