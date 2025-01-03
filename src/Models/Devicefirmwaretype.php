<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicefirmwaretype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicefirmwaretype';
  protected $titles = ['Firmware type', 'Firmware types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
