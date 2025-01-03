<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Manufacturer';
  protected $titles = ['Manufacturer', 'Manufacturers'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
