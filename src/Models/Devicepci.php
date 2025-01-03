<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicepci extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Devicepci';
  protected $titles = ['PCI device', 'PCI devices'];
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

  /** @return BelongsTo<\App\Models\Devicepcimodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicepcimodel::class, 'devicepcimodel_id');
  }

  /** @return HasMany<\App\Models\ItemDevicepci, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemDevicepci::class, 'devicepci_id');
  }
}
