<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computermodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Computermodel::class;
  protected $titles = ['Computer model', 'Computer models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
