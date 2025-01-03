<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Problem extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Problem';
  protected $titles = ['Problem', 'Problems'];
  protected $icon = 'drafting compass';

  protected $appends = [
  ];

  protected $visible = [
    'id',
    'name',
    'created_at',
    'updated_at',
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
    'changes',
    'costs',
    'items',
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
    'changes',
    'costs',
    'items',
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

  /** @return BelongsToMany<\App\Models\Change, $this> */
  public function changes(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Change::class, 'change_problem', 'problem_id', 'change_id');
  }

  /** @return HasMany<\App\Models\Problemcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Problemcost::class, 'problem_id');
  }

  /** @return HasMany<\App\Models\ItemProblem, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ItemProblem::class, 'problem_id');
  }
}
