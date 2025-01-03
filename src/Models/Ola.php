<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ola extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Ola';
  protected $titles = ['OLA', 'OLA'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'calendar',
  ];

  protected $with = [
    'entity:id,name,completename',
    'calendar:id,name',
  ];

  /** @return BelongsTo<\App\Models\Calendar, $this> */
  public function calendar(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Calendar::class, 'calendar_id');
  }
}
