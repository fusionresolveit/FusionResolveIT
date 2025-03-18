<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Audit::class;
  protected $titles = ['Audit', 'Audits'];
  protected $icon = 'scroll';
  protected $hasEntityField = false;
}
