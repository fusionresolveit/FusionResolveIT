<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Plug extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Plug';
  protected $titles = ['Plug', 'Plugs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
