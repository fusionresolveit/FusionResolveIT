<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requesttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Requesttype::class;
  protected $titles = ['Request source', 'Request sources'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
