<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Network extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Network';
  protected $titles = ['Network', 'Networks'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
