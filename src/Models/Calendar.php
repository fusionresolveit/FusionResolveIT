<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Calendar';
  protected $titles = ['Calendar', 'Calendars'];
  protected $icon = 'edit';


  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'timeranges',
    'holidays',
  ];

  protected $with = [
    'entity:id,name,completename',
    'timeranges',
    'holidays',
  ];

  /** @return HasMany<\App\Models\Calendarsegment, $this> */
  public function timeranges(): HasMany
  {
    return $this->hasMany(\App\Models\Calendarsegment::class, 'calendar_id');
  }

  /** @return BelongsToMany<\App\Models\Holiday, $this> */
  public function holidays(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Holiday::class, 'calendar_holiday', 'calendar_id', 'holiday_id');
  }
}
