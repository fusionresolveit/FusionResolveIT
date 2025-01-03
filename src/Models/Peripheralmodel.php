<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Peripheralmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Peripheralmodel';
  protected $titles = ['Peripheral model', 'Peripheral models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
