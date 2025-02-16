<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitormodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Monitormodel::class;
  protected $titles = ['Monitor model', 'Monitor models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
