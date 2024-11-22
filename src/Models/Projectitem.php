<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projectitem extends Common
{
  protected $definition = '\App\Models\Definitions\Projectitem';
  protected $titles = ['Project item', 'Project items'];
  protected $icon = 'columns';
  protected $table = 'item_project';
  protected $hasEntityField = false;
}
