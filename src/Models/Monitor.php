<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Monitor extends Common
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

  protected $definition = '\App\Models\Definitions\Monitor';
  protected $titles = ['Monitor', 'Monitors'];
  protected $icon = 'desktop';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'user',
    'group',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'softwareversions',
    'operatingsystems',
    'tickets',
    'problems',
    'changes',
    'connections',
    'infocom',
    'reservations',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
    'location:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'softwareversions:id,name',
    'operatingsystems:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'connections:id,name',
    'infocom',
    'reservations',
  ];


  /** @return BelongsTo<\App\Models\Monitortype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Monitortype::class, 'monitortype_id');
  }

  /** @return BelongsTo<\App\Models\Monitormodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Monitormodel::class, 'monitormodel_id');
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

  /** @return MorphToMany<\App\Models\Softwareversion, $this> */
  public function softwareversions(): MorphToMany
  {
    return $this->morphToMany(\App\Models\Softwareversion::class, 'item', 'item_softwareversion');
  }

  /** @return MorphToMany<\App\Models\Operatingsystem, $this> */
  public function operatingsystems(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Operatingsystem::class,
      'item',
      'item_operatingsystem'
    )->withPivot(
      'operatingsystemversion_id',
      'operatingsystemservicepack_id',
      'operatingsystemarchitecture_id',
      'operatingsystemkernelversion_id',
      'operatingsystemedition_id',
      'license_number',
      'licenseid',
      'installationdate',
      'winowner',
      'wincompany',
      'oscomment',
      'hostid'
    );
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function connections(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Computer::class,
      'item',
      'computer_item'
    )->withPivot(
      'computer_id',
      'is_dynamic',
    );
  }
}
