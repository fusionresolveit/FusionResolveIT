<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contracttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contracttype::class;
  protected $titles = ['Contract type', 'Contract types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
