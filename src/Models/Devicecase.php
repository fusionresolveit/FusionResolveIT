<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Devicecase extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicecase::class;
  protected $titles = ['Case', 'Cases'];
  protected $icon = 'case';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
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
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name,completename',
    'documents',
  ];

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return BelongsTo<\App\Models\Devicecasetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicecasetype::class, 'devicecasetype_id');
  }

  /** @return BelongsTo<\App\Models\Devicecasemodel, $this> */
  public function model(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Devicecasemodel::class, 'devicecasemodel_id');
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_devicecase');
  }
}
