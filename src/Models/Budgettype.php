<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Budgettype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Budgettype';
  protected $titles = ['Budget type', 'Budget types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
