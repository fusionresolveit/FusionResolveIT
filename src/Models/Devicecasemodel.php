<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecasemodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecasemodel';
  protected $titles = ['Device case model', 'Device case models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
