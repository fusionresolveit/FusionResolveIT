<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slm extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Slm';
  protected $titles = ['Service level', 'Service levels'];
  protected $icon = 'edit';

  protected $appends = [
    'calendar',
    'entity',
  ];

  protected $visible = [
    'calendar',
    'entity',
  ];

  protected $with = [
    'calendar:id,name',
    'entity:id,name',
  ];

  public function calendar(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Calendar');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
