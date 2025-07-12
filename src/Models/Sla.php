<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sla extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Sla::class;
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('ITIL', 'SLA', 'SLAs', $nb);
  }

  /** @return BelongsTo<\App\Models\Calendar, $this> */
  public function calendar(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Calendar::class, 'calendar_id');
  }
}
