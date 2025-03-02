<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budgettype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Budgettype::class;
  protected $titles = ['Budget type', 'Budget types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
