<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Cluster extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Contract;

  protected $definition = '\App\Models\Definitions\Cluster';
  protected $titles = ['Cluster', 'Clusters'];
  protected $icon = 'project diagram';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'state',
    'userstech',
    'groupstech',
    'entity',
    'appliances',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
    'entity:id,name,completename',
    'appliances:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
  ];

  /** @return BelongsTo<\App\Models\Clustertype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Clustertype::class, 'clustertype_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
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

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Appliance::class,
      'item',
      'appliance_item'
    );
  }
}
