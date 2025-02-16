<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printertype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Printertype::class;
  protected $titles = ['Printer type', 'Printer types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
