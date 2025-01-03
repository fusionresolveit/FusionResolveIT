<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Notes;

  protected $definition = '\App\Models\Definitions\Group';
  protected $titles = ['Group', 'Groups'];
  protected $icon = 'users';
  protected $tree = true;

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'entity',
    'notes',
    'completename',
    'parents',
  ];

  protected $with = [
    'entity:id,name,completename',
    'notes:id',
    'parents:id,name,group_id,entity_id',
  ];

  public function getCompletenameAttribute(): string
  {
    $names = [];
    if ($this->treepath != null)
    {
      $itemsId = str_split($this->treepath, 5);
      array_pop($itemsId);
      foreach ($itemsId as $key => $value)
      {
        $itemsId[$key] = (int) $value;
      }
      $items = \App\Models\Group::whereIn('id', $itemsId)->orderBy('treepath')->get();
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }

  /** @return HasMany<\App\Models\Group, $this> */
  public function parents(): HasMany
  {
    return $this->hasMany(\App\Models\Group::class, 'group_id');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function child(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id');
  }
}
