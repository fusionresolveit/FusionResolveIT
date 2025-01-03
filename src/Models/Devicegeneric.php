<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegeneric extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicegeneric';
  protected $titles = ['Generic device', 'Generic devices'];
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

  /** @return BelongsTo<\App\Models\Devicegenerictype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicegenerictype::class, 'devicegenerictype_id');
  }

  /** @return HasMany<\App\Models\ItemDevicegeneric, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicegeneric::class, 'devicegeneric_id');
  }
}
