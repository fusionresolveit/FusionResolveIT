<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Printertype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Printertype';
  protected $titles = ['Printer type', 'Printer types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
