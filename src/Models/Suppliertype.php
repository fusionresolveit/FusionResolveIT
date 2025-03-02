<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suppliertype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Suppliertype::class;
  protected $titles = ['Third party type', 'Third party types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
