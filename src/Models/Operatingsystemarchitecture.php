<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemarchitecture extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemarchitecture';
  protected $titles = ['Operating system architecture', 'Operating system architectures'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
