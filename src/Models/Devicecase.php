<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecase extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecase';
  protected $titles = ['Case', 'Cases'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'type',
    'model',
    'entity',
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'model',
    'entity',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicecasetype', 'devicecasetype_id');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicecasemodel', 'devicecasemodel_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
