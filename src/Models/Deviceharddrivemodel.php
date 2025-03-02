<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deviceharddrivemodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Deviceharddrivemodel::class;
  protected $titles = ['Device hard drive model', 'Device hard drive models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
