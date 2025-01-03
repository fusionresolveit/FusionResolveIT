<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicedrive extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicedrive';
  protected $titles = ['Drive', 'Drives'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'interface',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
    'interface:id,name',
    'entity:id,name,completename',
    'documents',
    'items',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicedrivemodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicedrivemodel::class, 'devicedrivemodel_id');
  }

  /** @return BelongsTo<\App\Models\Interfacetype, $this> */
  public function interface(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Interfacetype::class, 'interfacetype_id');
  }

  /** @return HasMany<\App\Models\ItemDevicedrive, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicedrive::class, 'devicedrive_id');
  }
}
