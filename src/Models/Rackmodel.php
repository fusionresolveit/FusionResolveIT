<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rackmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Rackmodel::class;
  protected $titles = ['Rack model', 'Rack models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
