<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pdu extends Common
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

  protected $definition = '\App\Models\Definitions\Pdu';
  protected $titles = ['PDU', 'PDUs'];
  protected $icon = 'plug';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
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
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
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
  public function groupstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsToMany<\App\Models\Plug, $this> */
  public function plugs(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Plug::class, 'pdu_plug', 'pdu_id', 'plug_id')->withPivot('number_plugs');
  }
}
