<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Appliance extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Appliance::class;
  protected $titles = ['Appliance', 'Appliances'];
  protected $icon = 'cubes';
  /** @var string[] */
  protected $cascadeDeletes = [
    'certificates',
    'domains',
    'itemComputers',
    'itemMonitors',
    'itemNetworkequipments',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
    'itemSoftwares',
    'itemClusters',
    'documents',
    'tickets',
    'problems',
    'changes',
    'Infocom',
    'contracts',
    'knowledgebasearticles',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'type',
    'state',
    'user',
    'group',
    'usertech',
    'grouptech',
    'manufacturer',
    'environment',
    'entity',
    'certificates',
    'domains',
    'knowledgebasearticles',
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
    'usertech:id,name,firstname,lastname',
    'grouptech:id,name,completename',
    'manufacturer:id,name',
    'environment:id,name',
    'entity:id,name,completename',
    'certificates:id,name',
    'domains:id,name',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
  ];

  /** @return BelongsTo<\App\Models\Appliancetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Appliancetype::class, 'appliancetype_id');
  }

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

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usertech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function grouptech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Applianceenvironment, $this> */
  public function environment(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Applianceenvironment::class, 'applianceenvironment_id');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Certificate::class,
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
    );
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

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'appliance_item');
  }

  /** @return MorphToMany<\App\Models\Cluster, $this> */
  public function itemClusters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cluster::class, 'item', 'appliance_item');
  }
}
