<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicenetworkcardmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicenetworkcardmodel::class;
  protected $titles = ['Network card model', 'Network card models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
