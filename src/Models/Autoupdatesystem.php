<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autoupdatesystem extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Autoupdatesystem::class;
  protected $titles = ['Update Source', 'Update Sources'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
