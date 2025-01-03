<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Monitortype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Monitortype';
  protected $titles = ['Monitor type', 'Monitor types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
