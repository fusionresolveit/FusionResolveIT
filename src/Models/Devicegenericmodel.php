<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegenericmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicegenericmodel';
  protected $titles = ['Device generic model', 'Device generic models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
