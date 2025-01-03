<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Software extends Common
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
  use \App\Traits\Relationships\Reservations;

  protected $table = 'softwares';
  protected $definition = '\App\Models\Definitions\Software';
  protected $titles = ['Software', 'Software'];
  protected $icon = 'cube';

  protected $appends = [
  ];

  protected $visible = [
    // 'category',
    // 'manufacturer',
    // // 'nbinstallation',
    // 'versions',
    // 'groupstech',
    // 'userstech',
    // 'user',
    // 'group',
    // 'location',
    'entity',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'reservations',
  ];

  protected $with = [
    // 'category:id,name',
    // 'manufacturer:id,name',
    // // 'nbinstallation.devices',
    'versions:id,name',
    // 'groupstech:id,name,completename',
    // 'userstech:id,name,firstname,lastname',
    // 'user:id,name,firstname,lastname',
    // 'group:id,name,completename',
    // 'location:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
    'reservations',
  ];

  protected $fillable = [
    'name',
    'entity_id'
  ];

  /** @return BelongsTo<\App\Models\Softwarecategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwarecategory::class, 'softwarecategory_id');
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  // public function nbinstallation(): HasMany
  // {
  //   return $this->hasMany(\App\Models\Softwareversion::class)->withCount('devices');
  // }

  /** @return HasMany<\App\Models\Softwareversion, $this> */
  public function versions(): HasMany
  {
    return $this->hasMany(\App\Models\Softwareversion::class);
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

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function domains(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Domain::class,
      'item',
      'domain_item'
    )->withPivot(
      'domainrelation_id',
    );
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
