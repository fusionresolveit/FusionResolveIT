<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Change extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Change';
  protected $titles = ['Change', 'Changes'];
  protected $icon = 'paint roller';

  protected $appends = [
  ];

  protected $visible = [
    'itilcategorie',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'knowbaseitems',
    'requester',
    'requestergroup',
    'technician',
    'techniciangroup',
    'costs',
    'items',
    'approvals',
  ];

  protected $with = [
    'itilcategorie:id,name',
    'usersidlastupdater:id,name,firstname,lastname',
    'usersidrecipient:id,name,firstname,lastname',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'requester:id,name,firstname,lastname',
    'requestergroup:id,name,completename',
    'technician:id,name,firstname,lastname',
    'techniciangroup:id,name,completename',
    'costs',
    'items',
    'approvals',
  ];

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function itilcategorie(): BelongsTo
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

  /** @return HasMany<\App\Models\Changecost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Changecost::class, 'change_id');
  }

  public function getFeeds($id): array
  {
    $feeds = [];

    return $feeds;
  }

  /** @return HasMany<\App\Models\ChangeItem, $this> */
  public function items(): HasMany
  {
    return $this->hasMany(\App\Models\ChangeItem::class, 'change_id');
  }

  /** @return HasMany<\App\Models\Changevalidation, $this> */
  public function approvals(): HasMany
  {
    return $this->hasMany(\App\Models\Changevalidation::class, 'change_id');
  }
}
