<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartridgeitem extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Notes;

  protected $definition = '\App\Models\Definitions\Cartridgeitem';
  protected $titles = ['Cartridge', 'Cartridges'];
  protected $icon = 'fill drip';

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
    'cartridges',
    'printermodels',
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
    'cartridges:id',
    'printermodels:id,name',
    'infocom',
  ];


  /** @return BelongsTo<\App\Models\Cartridgeitemtype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Cartridgeitemtype::class, 'cartridgeitemtype_id');
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

  /** @return HasMany<\App\Models\Cartridge, $this> */
  public function cartridges(): HasMany
  {
    return $this->hasMany(\App\Models\Cartridge::class, 'cartridgeitem_id');
  }

  /** @return BelongsToMany<\App\Models\Printermodel, $this> */
  public function printermodels(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Printermodel::class,
      'cartridgeitem_printermodel',
      'cartridgeitem_id',
      'printermodel_id'
    );
  }
}
