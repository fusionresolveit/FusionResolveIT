<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Computertype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Computertype';
  protected $titles = ['Computer type', 'Computer types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
