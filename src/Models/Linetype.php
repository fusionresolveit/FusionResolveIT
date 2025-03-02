<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Linetype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Linetype::class;
  protected $titles = ['Line type', 'Line types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
