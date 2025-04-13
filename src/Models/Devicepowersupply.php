<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Devicepowersupply extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicepowersupply::class;
  protected $titles = ['Power supply', 'Power supplies'];
  protected $icon = 'power-supply-unit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
    'itemNetworkequipments',
    'itemEnclosures',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'entity',
    'documents',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
    'entity:id,name,completename',
    'documents',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicepowersupplymodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicepowersupplymodel::class, 'devicepowersupplymodel_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'idem_devicepowersupply');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'idem_devicepowersupply');
  }

  /** @return MorphToMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Enclosure::class, 'item', 'idem_devicepowersupply');
  }
}
