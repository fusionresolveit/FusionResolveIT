<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowledgebasearticle extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Knowledgebasearticle::class;
  protected $titles = ['Knowledge base article', 'Knowledge base articles'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'user',
    'entitiesview',
    'groupsview',
    'profilesview',
    'usersview',
  ];

  protected $with = [
    'category:id,name,treepath',
    'user:id,name,firstname,lastname',
    'entitiesview:id,name,treepath',
    'groupsview:id,name,treepath',
    'profilesview:id,name',
    'usersview',
  ];

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class, 'category_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /** @return BelongsToMany<\App\Models\Entity, $this> */
  public function entitiesview(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Entity::class)->withPivot('is_recursive');
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function groupsview(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Group::class)->withPivot('is_recursive');
  }

  /** @return BelongsToMany<\App\Models\Profile, $this> */
  public function profilesview(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Profile::class);
  }

  /** @return BelongsToMany<\App\Models\User, $this> */
  public function usersview(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\User::class);
  }
}
