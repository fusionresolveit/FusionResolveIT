<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartridgeitemtype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Cartridgeitemtype::class;
  protected $titles = ['Cartridge type', 'Cartridge types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
