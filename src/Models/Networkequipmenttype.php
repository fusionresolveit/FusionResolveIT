<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Networkequipmenttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Networkequipmenttype::class;
  protected $titles = ['Networking equipment type', 'Networking equipment types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
