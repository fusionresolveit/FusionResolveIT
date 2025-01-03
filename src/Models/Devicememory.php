<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicememory extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicememory';
  protected $titles = ['Memory', 'Memory'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'model',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name,completename',
    'documents',
    'items',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicememorytype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicememorytype::class, 'devicememorytype_id');
  }

  /** @return BelongsTo<\App\Models\Devicememorymodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicememorymodel::class, 'devicememorymodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicememory, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicememory::class, 'devicememory_id');
  }
}
