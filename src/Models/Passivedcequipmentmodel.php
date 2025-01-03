<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Passivedcequipmentmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Passivedcequipmentmodel';
  protected $titles = ['Passive device model', 'Passive device models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
