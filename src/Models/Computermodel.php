<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Computermodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Computermodel';
  protected $titles = ['Computer model', 'Computer models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
