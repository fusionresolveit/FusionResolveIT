<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Project';
  protected $titles = ['Project', 'Projects'];
  protected $icon = 'columns';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tasks',
    'costs',
    'parents',
  ];

  protected $with = [
    'type:id,name',
    'state',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tasks',
    'parents:id,name',
    'costs:id,name',
  ];


  /** @return BelongsTo<\App\Models\Projecttype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttype::class, 'projecttype_id');
  }

  /** @return BelongsTo<\App\Models\Projectstate, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projectstate::class, 'projectstate_id');
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

  /** @return HasMany<\App\Models\Projecttask, $this> */
  public function tasks(): HasMany
  {
    return $this->hasMany(\App\Models\Projecttask::class, 'project_id');
  }

  /** @return HasMany<\App\Models\Project, $this> */
  public function parents(): HasMany
  {
    return $this->hasMany(\App\Models\Project::class, 'project_id');
  }

  /** @return HasMany<\App\Models\Projectcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Projectcost::class);
  }
}
