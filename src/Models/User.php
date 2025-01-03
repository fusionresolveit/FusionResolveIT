<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class User extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\User';
  protected $titles = ['User', 'Users'];
  protected $icon = 'user';

  protected $appends = [
    // 'category',
    // 'title',
    // 'location',
    // 'profile',
    // 'supervisor',
    // 'group',
    'completename',
    // 'entity',
    // 'certificates',
    // 'defaultgroup',
  ];

  protected $visible = [
    'category',
    'title',
    'location',
    'entity',
    'profile',
    'supervisor',
    'group',
    'completename',
    'certificates',
    'defaultgroup',
    'documents',
  ];

  protected $with = [
    'category:id,name',
    'title:id,name',
    'location:id,name',
    'entity:id,name,completename',
    'profile:id,name',
    'supervisor:id,name,firstname,lastname',
    'group:id,name,completename',
    'certificates:id,name',
    'defaultgroup:id,name',
    'documents:id,name',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /** @return BelongsTo<\App\Models\Usercategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Usercategory::class, 'usercategory_id');
  }

  /** @return BelongsTo<\App\Models\Usertitle, $this> */
  public function title(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Usertitle::class, 'usertitle_id');
  }

  /** @return BelongsTo<\App\Models\Profile, $this> */
  public function profile(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Profile::class);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function supervisor(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_supervisor');
  }

  /** @return BelongsToMany<\App\Models\Group, $this> */
  public function group(): BelongsToMany
  {
    return $this
      ->belongsToMany(\App\Models\Group::class)
      ->withPivot('group_id', 'is_dynamic', 'is_manager', 'is_userdelegate');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function defaultgroup(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id');
  }

  /** @return BelongsToMany<\App\Models\Profile, $this> */
  public function profiles(): BelongsToMany
  {
      return $this->belongsToMany(\App\Models\Profile::class)->withPivot('entity_id', 'is_recursive');
  }

  public function getCompletenameAttribute(): string
  {
    if ($this->id == 0)
    {
      return 'Nobody';
    }

    $name = '';
    if (
        (!is_null($this->lastname) && !empty($this->lastname)) ||
        (!is_null($this->firstname) && !empty($this->firstname))
    )
    {
      $names = [];
      if (!is_null($this->firstname))
      {
        $names[] = $this->firstname;
      }
      if (!is_null($this->lastname))
      {
        $names[] = $this->lastname;
      }
      $name = implode(' ', $names);
    }
    elseif (!is_null($this->name))
    {
      $name = $this->name;
    }
    return $name;
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
}
