<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Filesystem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Filesystem';
  protected $titles = ['File system', 'File systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
