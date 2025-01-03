<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicedrivemodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicedrivemodel';
  protected $titles = ['Device drive model', 'Device drive models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
