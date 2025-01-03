<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicefirmwaremodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicefirmwaremodel';
  protected $titles = ['Device firmware model', 'Device firmware models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
