<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printermodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Printermodel::class;
  protected $titles = ['Printer model', 'Printer models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
