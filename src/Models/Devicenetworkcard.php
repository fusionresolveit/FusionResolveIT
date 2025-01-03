<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicenetworkcard extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicenetworkcard';
  protected $titles = ['Network card', 'Network cards'];
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

  /** @return BelongsTo<\App\Models\Devicenetworkcardmodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicenetworkcardmodel::class, 'devicenetworkcardmodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicenetworkcard, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicenetworkcard::class, 'devicenetworkcard_id');
  }
}
