<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Contract extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contract::class;
  protected $titles = ['Contract', 'Contracts'];
  protected $icon = 'file signature';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'notes',
    'knowledgebasearticles',
    'suppliers',
    'costs',
    'itemAppliances',
    'itemCertificates',
    'itemClusters',
    'itemComputers',
    'itemDcrooms',
    'itemDomains',
    'itemEnclosures',
    'itemLines',
    'itemMonitors',
    'itemNetworkequipments',
    'itemPassivedcequipments',
    'itemPdus',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
    'itemProjects',
    'itemRacks',
    'itemSoftwares',
    'itemSoftwarelicenses',
    'itemSuppliers',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'state',
    'entity',
    'notes',
    'knowledgebasearticles',
    'documents',
    'suppliers',
    'costs',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'suppliers:id,name',
    'costs:id,name',
  ];

  /** @return BelongsTo<\App\Models\Contracttype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Contracttype::class, 'contracttype_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }

  /** @return BelongsToMany<\App\Models\Supplier, $this> */
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Supplier::class);
  }

  /** @return HasMany<\App\Models\Contractcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Contractcost::class);
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function itemAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function itemCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Cluster, $this> */
  public function itemClusters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Cluster::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Dcroom, $this> */
  public function itemDcrooms(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Dcroom::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function itemDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Line, $this> */
  public function itemLines(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Line::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Passivedcequipment, $this> */
  public function itemPassivedcequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Passivedcequipment::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Pdu, $this> */
  public function itemPdus(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Pdu::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Project, $this> */
  public function itemProjects(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Project::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Rack, $this> */
  public function itemRacks(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Rack::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function itemSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'contract_item');
  }

  /** @return MorphToMany<\App\Models\Supplier, $this> */
  public function itemSuppliers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Supplier::class, 'item', 'contract_item');
  }
}
