<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecontrolmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicecontrolmodel::class;
  protected $titles = ['Device control model', 'Device control models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
