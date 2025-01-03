<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ipnetwork extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Ipnetwork';
  protected $titles = ['IP network', 'IP networks'];
  protected $icon = 'edit';

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

  /** @return BelongsToMany<\App\Models\Vlan, $this> */
  public function vlans(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Vlan::class, 'ipnetworks_vlans', 'ipnetwork_id', 'vlan_id');
  }
}
