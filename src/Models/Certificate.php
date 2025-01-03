<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Common
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
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Certificate';
  protected $titles = ['Certificate', 'Certificates'];
  protected $icon = 'certificate';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'type',
    'state',
    'user',
    'group',
    'userstech',
    'groupstech',
    'manufacturer',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'infocom',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'state:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
    'manufacturer:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
  ];

  /** @return BelongsTo<\App\Models\Certificatetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Certificatetype::class, 'certificatetype_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class, 'state_id');
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

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function groupstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }
}
