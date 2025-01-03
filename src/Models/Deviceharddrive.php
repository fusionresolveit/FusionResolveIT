<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deviceharddrive extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Deviceharddrive';
  protected $titles = ['Hard drive', 'Hard drives'];
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

  /** @return BelongsTo<\App\Models\Deviceharddrivemodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Deviceharddrivemodel::class, 'deviceharddrivemodel_id');
  }

  /** @return BelongsTo<\App\Models\Interfacetype, $this> */
  public function interface(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Interfacetype::class, 'interfacetype_id');
  }

  /** @return HasMany<\App\Models\ItemDeviceharddrive, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDeviceharddrive::class, 'deviceharddrive_id');
  }
}
