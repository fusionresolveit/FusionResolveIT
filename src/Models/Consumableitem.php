<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Consumableitem extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Consumableitem::class;
  protected $titles = ['Consumable', 'Consumables'];
  protected $icon = 'box open';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'infocom',
    'notes',
    'consumables',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'manufacturer',
    'grouptech',
    'usertech',
    'location',
    'entity',
    'notes',
    'documents',
    'consumables',
    'infocom',
  ];

  protected $with = [
    'type:id,name',
    'manufacturer:id,name',
    'grouptech:id,name,completename',
    'usertech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
    'consumables:id',
    'infocom',
  ];


  /** @return BelongsTo<\App\Models\Consumableitemtype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Consumableitemtype::class, 'consumableitemtype_id');
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function grouptech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usertech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return HasMany<\App\Models\Consumable, $this> */
  public function consumables(): HasMany
  {
    return $this->hasMany(\App\Models\Consumable::class, 'consumableitem_id');
  }
}
