<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDevicesimcard extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  protected $definition = '\App\Models\Definitions\ItemDevicesimcard';
  protected $titles = ['Simcard', 'Simcards'];
  protected $icon = 'sim card';

  protected $table = 'item_devicesimcard';

  protected $appends = [
  ];

  protected $visible = [
    'state',
    'location',
    'user',
    'group',
    'entity',
    'documents',
    'contracts',
    'infocom',
  ];

  protected $with = [
    'state:id,name',
    'location:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'documents',
    'contracts:id,name',
    'infocom',
  ];


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
