<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Change extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Change::class;
  protected $titles = ['Change', 'Changes'];
  protected $icon = 'paint roller';

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'knowbaseitems',
    'requester',
    'requestergroup',
    'technician',
    'techniciangroup',
    'costs',
    'approvals',
    'problems',
  ];

  protected $with = [
    'category:id,name',
    'usersidlastupdater:id,name,firstname,lastname',
    'usersidrecipient:id,name,firstname,lastname',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'requester:id,name,firstname,lastname',
    'requestergroup:id,name,completename',
    'technician:id,name,firstname,lastname',
    'techniciangroup:id,name,completename',
    'costs',
    'approvals',
    'problems:id,name',
  ];

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class, 'category_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usersidlastupdater(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_lastupdater');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usersidrecipient(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_recipient');
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function requester(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class)->wherePivot('type', 1);
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function requestergroup(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->wherePivot('type', 1);
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function technician(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class)->wherePivot('type', 2);
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function techniciangroup(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->wherePivot('type', 2);
  }

  /** @return HasMany<\App\Models\Changecost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Changecost::class, 'change_id');
  }

  /**
   * @return array<mixed>
   */
  public function getFeeds(int $id): array
  {
    $feeds = [];

    return $feeds;
  }

  /** @return HasMany<\App\Models\Changevalidation, $this> */
  public function approvals(): HasMany
  {
    return $this->hasMany(\App\Models\Changevalidation::class, 'change_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function itemSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function itemCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Line, $this> */
  public function itemLines(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Line::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Dcroom, $this> */
  public function itemDcrooms(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Dcroom::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Rack, $this> */
  public function itemRacks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Rack::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Cluster, $this> */
  public function itemClusters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cluster::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Pdu, $this> */
  public function itemPdus(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Pdu::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function itemDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Domainrecord, $this> */
  public function itemDomainrecords(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domainrecord::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function itemAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Passivedcequipment, $this> */
  public function itemPassivedcequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Passivedcequipment::class, 'item', 'change_item');
  }

  /** @return MorphToMany<\App\Models\Project, $this> */
  public function projects(): MorphToMany
  {
    return $this->morphToMany(\App\Models\Project::class, 'item', 'itil_project');
  }

  /** @return BelongsToMany<\App\Models\Problem, $this> */
  public function problems(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Problem::class, 'change_problem', 'change_id', 'problem_id');
  }
}
