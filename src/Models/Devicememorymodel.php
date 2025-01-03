<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicememorymodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicememorymodel';
  protected $titles = ['Device memory model', 'Device memory models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
