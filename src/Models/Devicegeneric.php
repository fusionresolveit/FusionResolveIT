<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegeneric extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicegeneric';
  protected $titles = ['Generic device', 'Generic devices'];
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
    return $this->belongsTo('\App\Models\Devicegenerictype', 'devicegenerictype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
