<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filesystem extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Filesystem::class;
  protected $titles = ['File system', 'File systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
