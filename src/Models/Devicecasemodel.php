<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecasemodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicecasemodel::class;
  protected $titles = ['Device case model', 'Device case models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
