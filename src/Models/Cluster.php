<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Cluster extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Cluster';
  protected $titles = ['Cluster', 'Clusters'];
  protected $icon = 'project diagram';

  protected $appends = [
    'type',
    'state',
    'userstech',
    'groupstech',
    'entity',
    'appliances',
  ];

  protected $visible = [
    'type',
    'state',
    'userstech',
    'groupstech',
    'entity',
    'appliances',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'userstech:id,name',
    'groupstech:id,name',
    'entity:id,name',
    'appliances:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Clustertype', 'clustertype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Appliance',
      'item',
      'appliance_item'
    );
  }
}
