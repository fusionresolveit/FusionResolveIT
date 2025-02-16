<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Network extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Network::class;
  protected $titles = ['Network', 'Networks'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
