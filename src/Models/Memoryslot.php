<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Memoryslot extends Common
{
  use CascadesDeletes;
  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Memoryslot::class;
  protected $titles = ['Memory slot', 'Memory slot'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
  ];

  protected $appends = [
    'name',
  ];

  protected $visible = [
    'name',
  ];

  protected $casts = [
    'is_dynamic' => 'boolean',
  ];

  protected $with = [
  ];

  public function getNameAttribute(): string
  {
    global $translator;
    $item = $this->item;
    if (is_null($item))
    {
      return $translator->translate('slot') . ' ' . $this->attributes['slotnumber'];
    }
    if ($item instanceof \App\Models\Common)
    {
      return '(' . $item->getTitle() . ') ' . $item->getAttribute('name') . ' > ' .
          $translator->translate('slot') . ' ' . $this->attributes['slotnumber'];
    }

    return $item->getAttribute('name') . ' > ' . $translator->translate('slot') . ' ' .
           $this->attributes['slotnumber'];
  }

  /**
   * Get the parent item model.
   * @return MorphTo<\Illuminate\Database\Eloquent\Model, $this>
  */
  public function item(): MorphTo
  {
    return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
  }

  /** @return HasOne<\App\Models\Memorymodule, $this> */
  public function memorymodule(): HasOne
  {
    return $this->hasOne(\App\Models\Memorymodule::class);
  }
}
