<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Cartridgeitem extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Cartridgeitem::class;
  protected $titles = ['Cartridge', 'Cartridges'];
  protected $icon = 'fill drip';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'infocom',
    'notes',
    'cartridges',
    'printermodels',
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
    'cartridges',
    'printermodels',
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
  public function grouptech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usertech(): BelongsTo
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
