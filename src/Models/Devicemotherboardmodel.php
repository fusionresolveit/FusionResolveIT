<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicemotherboardmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicemotherboardmodel';
  protected $titles = ['System board model', 'System board models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
