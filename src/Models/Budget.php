<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Budget::class;
  protected $titles = ['Budget', 'Budgets'];
  protected $icon = 'calculator';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'type',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\Budgettype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budgettype::class, 'budgettype_id');
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function itemAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Cartridgeitem, $this> */
  public function itemCartridgeitems(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cartridgeitem::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function itemCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Consumableitem, $this> */
  public function itemConsumableitems(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Consumableitem::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Dcroom, $this> */
  public function itemDcrooms(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Dcroom::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function itemDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Line, $this> */
  public function itemLines(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Line::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Passivedcequipment, $this> */
  public function itemPassivedcequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Passivedcequipment::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Pdu, $this> */
  public function itemPdus(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Pdu::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Rack, $this> */
  public function itemRacks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Rack::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function itemSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'infocoms')
      ->withPivot('value');
  }

  /** @return BelongsToMany<\App\Models\Contract, $this> */
  public function itemContracts(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Contract::class,
      'contractcosts'
    )->withPivot('cost');
  }

  /** @return BelongsToMany<\App\Models\Ticket, $this> */
  public function itemTickets(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Ticket::class,
      'ticketcosts'
    )->withPivot('actiontime', 'cost_time', 'cost_fixed', 'cost_material');
  }

  /** @return BelongsToMany<\App\Models\Problem, $this> */
  public function itemProblems(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Problem::class,
      'problemcosts'
    )->withPivot('actiontime', 'cost_time', 'cost_fixed', 'cost_material');
  }

  /** @return BelongsToMany<\App\Models\Change, $this> */
  public function itemChanges(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Change::class,
      'changecosts'
    )->withPivot('actiontime', 'cost_time', 'cost_fixed', 'cost_material');
  }

  /** @return BelongsToMany<\App\Models\Project, $this> */
  public function itemProjects(): BelongsToMany
  {
    return $this->belongsToMany(
      \App\Models\Project::class,
      'projectcosts'
    )->withPivot('cost');
  }
}
