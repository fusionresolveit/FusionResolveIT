<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecontrol extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicecontrol';
  protected $titles = ['Controller', 'Controllers'];
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

  /** @return BelongsTo<\App\Models\Devicecontrolmodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicecontrolmodel::class, 'devicecontrolmodel_id');
  }

  /** @return BelongsTo<\App\Models\Interfacetype, $this> */
  public function interface(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Interfacetype::class, 'interfacetype_id');
  }

  /** @return HasMany<\App\Models\ItemDevicecontrol, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicecontrol::class, 'devicecontrol_id');
  }
}
