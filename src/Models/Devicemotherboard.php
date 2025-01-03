<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicemotherboard extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicemotherboard';
  protected $titles = ['System board', 'System boards'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
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

  /** @return BelongsTo<\App\Models\Devicemotherboardmodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicemotherboardmodel::class, 'devicemotherboardmodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicemotherboard, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicemotherboard::class, 'devicemotherboard_id');
  }
}
