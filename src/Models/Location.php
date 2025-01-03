<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Location';
  protected $titles = ['Location', 'Locations'];
  protected $icon = 'edit';
  protected $tree = true;

  protected $appends = [
    // 'completename',
  ];

  protected $visible = [
    'location',
    'entity',
    'documents',
    'completename',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
    'documents:id,name',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::created(function ($model)
    {
      // Manage tree
      $currItem = (new self())->find($model->id);
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->location_id > 0)
      {
        $parentItem = (new self())->find($currItem->location_id);
        $currItem->treepath = $parentItem->treepath . $currItem->treepath;
      }
      $currItem->name = 'YOLO';
      $currItem->save();
    });
  }

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
      $items = \App\Models\Location::whereIn('id', $itemsId)->orderBy('treepath')->get();
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }
}
