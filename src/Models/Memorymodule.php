<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Memorymodule extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Memorymodule::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'model',
    'entity',
    'documents',
  ];

  protected $with = [
    'memoryslot',
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name,completename',
    'documents',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Memory module', 'Memory modules', $nb);
  }

  protected $casts = [
    'fusioninventoried_at' => 'datetime:Y-m-d H:i:s',
  ];

  /** @return BelongsTo<\App\Models\Memoryslot, $this> */
  public function memoryslot(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Memoryslot::class);
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Memorytype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Memorytype::class, 'memorytype_id');
  }

  /** @return BelongsTo<\App\Models\Memorymodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Memorymodel::class, 'memorymodel_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_devicememory');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'item_devicememory');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'item_devicememory');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'item_devicememory');
  }
}
