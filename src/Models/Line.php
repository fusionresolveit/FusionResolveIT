<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Line extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Line::class;
  protected $icon = 'phone';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'infocom',
    'contracts',
    'notes',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'type',
    'operator',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'documents',
    'contracts',
    'infocom',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'operator:id,name',
    'state:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
    'contracts:id,name',
    'infocom',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Line', 'Lines', $nb);
  }

  /** @return BelongsTo<\App\Models\Linetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Linetype::class, 'linetype_id');
  }

  /** @return BelongsTo<\App\Models\Lineoperator, $this> */
  public function operator(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Lineoperator::class, 'lineoperator_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function group(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class);
  }
}
