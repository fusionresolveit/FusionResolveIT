<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemversion extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemversion';
  protected $titles = ['Version of the operating system', 'Versions of the operating systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $casts = [
    'is_lts' => 'boolean',
  ];
}
