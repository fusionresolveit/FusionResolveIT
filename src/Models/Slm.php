<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Slm extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Slm::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'slas',
    'olas',
  ];

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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Service level', 'Service levels', $nb);
  }

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
