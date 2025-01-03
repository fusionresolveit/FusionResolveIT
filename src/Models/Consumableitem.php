<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumableitem extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Notes;

  protected $definition = '\App\Models\Definitions\Consumableitem';
  protected $titles = ['Consumable', 'Consumables'];
  protected $icon = 'box open';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'manufacturer',
    'groupstech',
    'userstech',
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
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
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
  public function groupstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return HasMany<\App\Models\Consumable, $this> */
  public function consumables(): HasMany
  {
    return $this->hasMany(\App\Models\Consumable::class, 'consumableitem_id');
  }
}
