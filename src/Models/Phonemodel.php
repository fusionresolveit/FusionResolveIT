<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Phonemodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Phonemodel';
  protected $titles = ['Phone model', 'Phone models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
