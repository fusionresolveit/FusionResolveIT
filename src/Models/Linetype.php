<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Linetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Linetype';
  protected $titles = ['Line type', 'Line types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
