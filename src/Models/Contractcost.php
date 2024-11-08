<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractcost extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Contractcost';
  protected $titles = ['Contract cost', 'Contract costs'];
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
