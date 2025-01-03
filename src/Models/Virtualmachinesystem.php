<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Virtualmachinesystem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Virtualmachinesystem';
  protected $titles = ['Virtualization model', 'Virtualization models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
