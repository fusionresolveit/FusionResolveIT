<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Pdu extends Common
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

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Pdu::class;
  protected $icon = 'plug';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
    'plugs',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'grouptech',
    'usertech',
    'location',
    'entity',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'plugs',
    'infocom',
  ];

  protected $with = [
    'type:id,name',
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'grouptech:id,name,completename',
    'usertech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'plugs',
    'infocom',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'PDU', 'PDUs', $nb);
  }

  /** @return BelongsTo<\App\Models\Pdutype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Pdutype::class, 'pdutype_id');
  }

  /** @return BelongsTo<\App\Models\Pdumodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Pdumodel::class, 'pdumodel_id');
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

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function grouptech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usertech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsToMany<\App\Models\Plug, $this> */
  public function plugs(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Plug::class, 'pdu_plug', 'pdu_id', 'plug_id')->withPivot('number_plugs');
  }
}
