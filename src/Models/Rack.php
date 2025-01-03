<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rack extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  protected $definition = '\App\Models\Definitions\Rack';
  protected $titles = ['Rack', 'Racks'];
  protected $icon = 'print';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'infocom',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
  ];


  /** @return BelongsTo<\App\Models\Racktype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Racktype::class, 'racktype_id');
  }

  /** @return BelongsTo<\App\Models\Rackmodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Rackmodel::class, 'rackmodel_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
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
}
