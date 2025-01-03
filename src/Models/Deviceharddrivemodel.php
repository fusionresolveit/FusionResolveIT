<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Deviceharddrivemodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Deviceharddrivemodel';
  protected $titles = ['Device hard drive model', 'Device hard drive models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
