<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Calendar extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Calendar::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'timeranges',
    'holidays',
  ];

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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Calendar', 'Calendars', $nb);
  }

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
