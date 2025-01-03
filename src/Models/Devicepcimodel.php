<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicepcimodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicepcimodel';
  protected $titles = ['Other component model', 'Other component models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
