<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Projecttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Projecttype';
  protected $titles = ['Project type', 'Project types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
