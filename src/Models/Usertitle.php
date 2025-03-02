<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usertitle extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Usertitle::class;
  protected $titles = ['User title', 'Users titles'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
