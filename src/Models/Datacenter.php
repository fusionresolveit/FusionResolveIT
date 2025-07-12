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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Data center', 'Data centers', $nb);
  }

  /** @return HasMany<\App\Models\Dcroom, $this> */
  public function dcrooms(): HasMany
  {
    return $this->hasMany(\App\Models\Dcroom::class);
  }
}
