<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesimcardtype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicesimcardtype';
  protected $titles = ['Simcard type', 'Simcard types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
