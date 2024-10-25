<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Supplier';
  protected $titles = ['Supplier', 'Suppliers'];
  protected $icon = 'dolly';

  protected $appends = [
    'type',
    'entity',
  ];

  protected $visible = [
    'type',
    'entity',
  ];

  protected $with = [
    'type:id,name',
    'entity:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Suppliertype', 'suppliertype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
