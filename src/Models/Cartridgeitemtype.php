<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Cartridgeitemtype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Cartridgeitemtype';
  protected $titles = ['Cartridge type', 'Cartridge types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
