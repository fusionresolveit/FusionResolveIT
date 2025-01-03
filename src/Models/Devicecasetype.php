<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecasetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecasetype';
  protected $titles = ['Case type', 'Case types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
