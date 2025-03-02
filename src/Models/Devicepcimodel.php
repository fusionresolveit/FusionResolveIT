<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicepcimodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicepcimodel::class;
  protected $titles = ['Other component model', 'Other component models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
