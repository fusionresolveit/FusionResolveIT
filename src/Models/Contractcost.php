<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractcost extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contractcost::class;
  protected $titles = ['Contract cost', 'Contract costs'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'budget',
  ];

  protected $with = [
    'budget:id,name',
  ];

  /** @return BelongsTo<\App\Models\Budget, $this> */
  public function budget(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budget::class);
  }
}
