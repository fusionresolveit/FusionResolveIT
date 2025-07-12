<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Devicesimcard extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesimcard::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
    'itemNetworkequipments',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'SIM card', 'SIM cards', $nb);
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicesimcardtype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicesimcardtype::class, 'devicesimcardtype_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_devicesimcard');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'item_devicesimcard');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'item_devicesimcard');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'item_devicesimcard');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'item_devicesimcard');
  }
}
