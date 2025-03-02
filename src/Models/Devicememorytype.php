<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicememorytype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicememorytype::class;
  protected $titles = ['Memory type', 'Memory types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
