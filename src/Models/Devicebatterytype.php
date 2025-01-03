<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicebatterytype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicebatterytype';
  protected $titles = ['Battery type', 'Battery types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
