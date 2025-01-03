<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicememorytype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicememorytype';
  protected $titles = ['Memory type', 'Memory types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
