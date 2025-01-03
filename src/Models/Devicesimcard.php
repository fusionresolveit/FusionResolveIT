<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesimcard extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicesimcard';
  protected $titles = ['Simcard', 'Simcards'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'entity:id,name,completename',
    'documents',
    'items',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicesimcardtype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicesimcardtype::class, 'devicesimcardtype_id');
  }

  /** @return HasMany<\App\Models\ItemDevicesimcard, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicesimcard::class, 'devicesimcard_id');
  }
}
