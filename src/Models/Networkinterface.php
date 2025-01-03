<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Networkinterface extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Networkinterface';
  protected $titles = ['Network interface', 'Network interfaces'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
