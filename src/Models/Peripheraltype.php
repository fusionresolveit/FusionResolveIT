<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Peripheraltype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Peripheraltype';
  protected $titles = ['Peripheral type', 'Peripheral types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
