<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Printermodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Printermodel';
  protected $titles = ['Printer model', 'Printer models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
