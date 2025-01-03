<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Networkequipmenttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Networkequipmenttype';
  protected $titles = ['Networking equipment type', 'Networking equipment types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
