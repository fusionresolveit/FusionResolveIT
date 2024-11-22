<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projectcost extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Projectcost';
  protected $titles = ['Project cost', 'Project costs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
    'budget',
  ];

  protected $visible = [
    'budget',
  ];

  protected $with = [
    'budget:id,name',
  ];

  public function budget(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Budget');
  }
}
