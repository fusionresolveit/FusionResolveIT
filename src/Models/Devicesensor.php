<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Devicesensor extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesensor::class;
  protected $titles = ['Sensor', 'Sensors'];
  protected $icon = 'sensor';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
    'itemPeripherals',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'entity',
    'documents',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'entity:id,name,completename',
    'documents',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicesensortype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicesensortype::class, 'devicesensortype_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_devicesensor');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'item_devicesensor');
  }
}
