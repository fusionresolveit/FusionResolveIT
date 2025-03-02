<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phonepowersupply extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Phonepowersupply::class;
  protected $titles = ['Phone power supply type', 'Phone power supply types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
