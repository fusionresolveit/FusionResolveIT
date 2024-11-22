<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projectteam extends Common
{
  protected $definition = '\App\Models\Definitions\Projectteam';
  protected $titles = ['Project team', 'Project teams'];
  protected $icon = 'columns';
  protected $hasEntityField = false;
}
