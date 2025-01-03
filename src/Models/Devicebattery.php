<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicebattery extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicebattery';
  protected $titles = ['Battery', 'Batteries'];
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

  /** @return BelongsTo<\App\Models\Devicebatterytype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicebatterytype::class, 'devicebatterytype_id');
  }

  /** @return HasMany<\App\Models\ItemDevicebattery, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicebattery::class, 'devicebattery_id');
  }
}
