<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Certificate extends Common
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
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Certificate::class;
  protected $icon = 'certificate';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
    'notes',
    'knowledgebasearticles',
    'associatedAppliances',
    'associatedComputers',
    'associatedPeripherals',
    'associatedDomains',
    'associatedSoftwarelicenses',
    'associatedNetworkequipments',
    'associatedPhones',
    'associatedPrinters',
    'associatedUsers',
    'domains',
  ];

  protected $appends = [];

  protected $visible = [
    'location',
    'type',
    'state',
    'user',
    'group',
    'usertech',
    'grouptech',
    'manufacturer',
    'entity',
    'notes',
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
    'entity:id,name,completename',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'contracts:id,name',
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
    return npgettext('global', 'Certificate', 'Certificates', $nb);
  }

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

  /** @return MorphToMany<\App\Models\Appliance, $this> */
  public function associatedAppliances(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Appliance::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function associatedComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function associatedPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function associatedDomains(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Domain::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Softwarelicense, $this> */
  public function associatedSoftwarelicenses(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Softwarelicense::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function associatedNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function associatedPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function associatedPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\User, $this> */
  public function associatedUsers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\User::class, 'item', 'certificate_item');
  }

  /** @return MorphToMany<\App\Models\Domain, $this> */
  public function domains(): MorphToMany
  {
    return $this->morphToMany(\App\Models\Domain::class, 'item', 'domain_item')->withPivot('domainrelation_id');
  }
}
