<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegenerictype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicegenerictype';
  protected $titles = ['Generic type', 'Generic types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
