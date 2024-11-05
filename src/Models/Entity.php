<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Entity extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Entity';
  protected $titles = ['Entity', 'Entities'];
  protected $icon = 'layer group';
  protected $hasEntityField = false;

  protected $appends = [
    'notes',
  ];

  protected $visible = [
    'notes',
  ];

  protected $with = [
    'entity:id,name',
    'notes:id',
  ];

  public static function booted()
  {
    parent::booted();
    static::created(function ($model)
    {
      // Manage tree
      $currItem = (new self())->find($model->id);
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->entity_id > 0)
      {
        $parentItem = self::find($currItem->entity_id);
        $currItem->treepath = $parentItem->treepath . $currItem->treepath;
      }
      $currItem->save();
    });
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }
}
