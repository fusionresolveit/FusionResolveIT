<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesensortype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicesensortype';
  protected $titles = ['Sensor type', 'Sensor types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
