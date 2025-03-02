<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contacttype::class;
  protected $titles = ['Contact type', 'Contact types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
