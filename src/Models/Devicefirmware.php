<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicefirmware extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicefirmware';
  protected $titles = ['Firmware', 'Firmware'];
  protected $icon = 'edit';

  protected $table = "devicefirmwares";

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

  /** @return BelongsTo<\App\Models\Devicefirmwaretype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicefirmwaretype::class, 'devicefirmwaretype_id');
  }

  /** @return BelongsTo<\App\Models\Devicefirmwaremodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicefirmwaremodel::class, 'devicefirmwaremodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicefirmware, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicefirmware::class, 'devicefirmware_id');
  }
}
