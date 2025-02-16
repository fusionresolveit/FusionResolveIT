<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phonemodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Phonemodel::class;
  protected $titles = ['Phone model', 'Phone models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
