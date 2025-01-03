<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemedition extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemedition';
  protected $titles = ['Edition', 'Editions'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
