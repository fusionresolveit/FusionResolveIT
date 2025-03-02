<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicedrivemodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicedrivemodel::class;
  protected $titles = ['Device drive model', 'Device drive models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
