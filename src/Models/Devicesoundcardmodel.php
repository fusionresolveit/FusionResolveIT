<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesoundcardmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesoundcardmodel::class;
  protected $titles = ['Device sound card model', 'Device sound card models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
