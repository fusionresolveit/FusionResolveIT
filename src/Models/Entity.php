<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Entity';
  protected $titles = ['Entity', 'Entities'];
  protected $icon = 'layer group';
  protected $hasEntityField = false;

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'notes',
    'knowbaseitems',
    'documents',
    'completename',
    'profilesusers',
  ];

  protected $with = [
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'profilesusers',
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
      $items = \App\Models\Entity::whereIn('id', $itemsId)->orderBy('treepath')->get();
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }

  /** @return hasMany<\App\Models\ProfileUser, $this> */
  public function profilesusers(): HasMany
  {
    return $this->hasMany(\App\Models\ProfileUser::class, 'entity_id');
  }
}
