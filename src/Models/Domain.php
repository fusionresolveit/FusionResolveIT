<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Domain extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  protected $definition = '\App\Models\Definitions\Domain';
  protected $titles = ['Domain', 'Domains'];
  protected $icon = 'globe americas';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'userstech',
    'groupstech',
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
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
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

  /** @return BelongsTo<\App\Models\Domaintype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Domaintype::class, 'domaintype_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userstech(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function groupstech(): BelongsTo
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
}
