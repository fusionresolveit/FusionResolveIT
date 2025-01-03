<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Rackmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Rackmodel';
  protected $titles = ['Rack model', 'Rack models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
