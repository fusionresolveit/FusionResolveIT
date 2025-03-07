<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Softwarelicense extends Common
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
  use \App\Traits\Relationships\Knowbaseitems;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Softwarelicense::class;
  protected $titles = ['License', 'Licenses'];
  protected $icon = 'key';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
    'notes',
    'knowbaseitems',
    'certificates',
    'childs',
    'computers',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'softwarelicensetype',
    'usertech',
    'grouptech',
    'user',
    'group',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer',
    'software',
    'entity',
    'certificates',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'childs',
    'infocom',
  ];

  protected $with = [
    'location:id,name',
    'softwarelicensetype',
    'usertech:id,name,firstname,lastname',
    'grouptech:id,name,completename',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer:id,name',
    'software:id,name',
    'entity:id,name,completename',
    'certificates:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'childs:id,name',
    'infocom',
  ];

  /** @return BelongsTo<\App\Models\Softwarelicensetype, $this> */
  public function softwarelicensetype(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwarelicensetype::class);
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

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class, 'state_id');
  }

  /** @return BelongsTo<\App\Models\Softwareversion, $this> */
  public function softwareversionsBuy(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwareversion::class, 'softwareversion_id_buy');
  }

  /** @return BelongsTo<\App\Models\Softwareversion, $this> */
  public function softwareversionsUse(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwareversion::class, 'softwareversion_id_use');
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Software, $this> */
  public function software(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Software::class);
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

  /** @return HasMany<\App\Models\Softwarelicense, $this> */
  public function childs(): HasMany
  {
    return $this->hasMany(\App\Models\Softwarelicense::class);
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function computers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_softwarelicense');
  }
}
