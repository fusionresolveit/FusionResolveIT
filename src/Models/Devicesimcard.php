<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesimcard extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicesimcard';
  protected $titles = ['Simcard', 'Simcards'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'type',
    'entity',
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'entity',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'entity:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicesimcardtype', 'devicesimcardtype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
