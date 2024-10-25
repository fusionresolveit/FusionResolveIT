<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicepowersupply extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicepowersupply';
  protected $titles = ['Power supply', 'Power supplies'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'model',
    'entity',
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'entity',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
    'entity:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicepowersupplymodel', 'devicepowersupplymodel_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
