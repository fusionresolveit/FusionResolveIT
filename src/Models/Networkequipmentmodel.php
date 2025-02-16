<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Networkequipmentmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Networkequipmentmodel::class;
  protected $titles = ['Networking equipment model', 'Networking equipment models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
