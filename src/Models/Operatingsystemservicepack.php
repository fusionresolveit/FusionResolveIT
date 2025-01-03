<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemservicepack extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemservicepack';
  protected $titles = ['Service pack', 'Service packs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
