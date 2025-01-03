<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slm extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Slm';
  protected $titles = ['Service level', 'Service levels'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'calendar',
    'entity',
    'slas',
    'olas',
  ];

  protected $with = [
    'calendar:id,name',
    'entity:id,name,completename',
    'slas',
    'olas',
  ];

  /** @return BelongsTo<\App\Models\Calendar, $this> */
  public function calendar(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Calendar::class);
  }

  /** @return HasMany<\App\Models\Sla, $this> */
  public function slas(): HasMany
  {
    return $this->hasMany(\App\Models\Sla::class, 'slm_id');
  }

  /** @return HasMany<\App\Models\Ola, $this> */
  public function olas(): HasMany
  {
    return $this->hasMany(\App\Models\Ola::class, 'slm_id');
  }
}
