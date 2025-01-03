<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Suppliertype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Suppliertype';
  protected $titles = ['Third party type', 'Third party types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
