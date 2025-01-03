<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Infocom extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Infocom';
  protected $titles = ['Infocom', 'Infocoms'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'supplier',
    'budget',
    'businesscriticity',
  ];

  protected $with = [
    'entity:id,name,completename',
    'supplier:id,name',
    'budget:id,name',
    'businesscriticity:id,name',
  ];

  /** @return BelongsTo<\App\Models\Supplier, $this> */
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
  }

  /** @return BelongsTo<\App\Models\Budget, $this> */
  public function budget(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budget::class, 'budget_id');
  }

  /** @return BelongsTo<\App\Models\Businesscriticity, $this> */
  public function businesscriticity(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Businesscriticity::class, 'businesscriticity_id');
  }
}
