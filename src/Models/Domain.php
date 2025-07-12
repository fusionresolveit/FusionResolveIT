<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Domain extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Domain::class;
  protected $icon = 'globe americas';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
    'certificates',
    'records',
    'itemAppliances',
    'itemCertificates',
    'itemComputers',
    'itemPeripherals',
    'itemMonitors',
    'itemNetworkequipments',
    'itemPhones',
    'itemPrinters',
    'itemSoftwares',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'usertech',
    'grouptech',
    'entity',
    'certificates',
    'documents',
    'contracts',
    'records',
    'tickets',
    'problems',
    'changes',
    'infocom',
  ];

  protected $with = [
    'type:id,name',
    'usertech:id,name,firstname,lastname',
    'grouptech:id,name,completename',
    'entity:id,name,completename',
    'certificates:id,name',
    'documents:id,name',
    'contracts:id,name',
    'records:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Domain', 'Domains', $nb);
  }

  /** @return BelongsTo<\App\Models\Domaintype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Domaintype::class, 'domaintype_id');
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

  /** @return HasMany<\App\Models\Domainrecord, $this> */
  public function records(): HasMany
  {
    return $this->hasMany(\App\Models\Domainrecord::class);
  }

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function itemAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Certificate, $this> */
  public function itemCertificates(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Certificate::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'domain_item');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'domain_item');
  }
}
