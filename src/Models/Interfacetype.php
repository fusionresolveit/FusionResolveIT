<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Interfacetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Interfacetype';
  protected $titles = ['Interface type (Hard drive...)', 'Interface types (Hard drive...)'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
