<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegraphiccard extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicegraphiccard';
  protected $titles = ['Graphics card', 'Graphics cards'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'model',
    'interface',
    'entity',
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'interface',
    'entity',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
    'interface:id,name',
    'entity:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicegraphiccardmodel', 'devicegraphiccardmodel_id');
  }

  public function interface(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Interfacetype', 'interfacetype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
