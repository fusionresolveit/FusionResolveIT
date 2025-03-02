<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deviceprocessormodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Deviceprocessormodel::class;
  protected $titles = ['Device processor model', 'Device processor models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
