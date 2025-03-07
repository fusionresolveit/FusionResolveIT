<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Datacenter extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Datacenter::class;
  protected $titles = ['Data center', 'Data centers'];
  protected $icon = 'warehouse';
  /** @var string[] */
  protected $cascadeDeletes = [
    'dcrooms',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'entity',
    'dcrooms',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
    'dcrooms',
  ];

  /** @return HasMany<\App\Models\Dcroom, $this> */
  public function dcrooms(): HasMany
  {
    return $this->hasMany(\App\Models\Dcroom::class);
  }
}
