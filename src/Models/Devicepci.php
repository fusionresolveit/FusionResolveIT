<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Devicepci extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicepci::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'PCI device', 'PCI devices', $nb);
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicepcimodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicepcimodel::class, 'devicepcimodel_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_devicepci');
  }
}
