<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usercategory extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Usercategory::class;
  protected $titles = ['User category', 'User categories'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
