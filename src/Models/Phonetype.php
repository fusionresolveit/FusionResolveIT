<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phonetype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Phonetype::class;
  protected $titles = ['Phone type', 'Phone types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
