<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecase extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicecase';
  protected $titles = ['Case', 'Cases'];
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

  /** @return BelongsTo<\App\Models\Devicecasetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicecasetype::class, 'devicecasetype_id');
  }

  /** @return BelongsTo<\App\Models\Devicecasemodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicecasemodel::class, 'devicecasemodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicecase, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicecase::class, 'devicecase_id');
  }
}
