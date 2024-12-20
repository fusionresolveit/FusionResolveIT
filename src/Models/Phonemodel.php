<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phonemodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Phonemodel';
  protected $titles = ['Phone model', 'Phone models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
