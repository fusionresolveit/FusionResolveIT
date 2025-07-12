<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Ipnetwork extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Ipnetwork::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'vlans',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'vlans',
  ];

  protected $with = [
    'entity:id,name,completename',
    'vlans',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'IP network', 'IP networks', $nb);
  }

  /** @return BelongsToMany<\App\Models\Vlan, $this> */
  public function vlans(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Vlan::class, 'ipnetworks_vlans', 'ipnetwork_id', 'vlan_id');
  }
}
