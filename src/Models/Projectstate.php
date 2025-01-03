<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Projectstate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Projectstate';
  protected $titles = ['Project state', 'Project states'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
