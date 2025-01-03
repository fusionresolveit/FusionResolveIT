<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dcroom extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  protected $definition = '\App\Models\Definitions\Dcroom';
  protected $titles = ['Server room', 'Server rooms'];
  protected $icon = 'warehouse';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'entity',
    'infocom',
    'contracts',
    'documents',
    'tickets',
    'problems',
    'changes',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
    'infocom',
    'contracts:id,name',
    'documents:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
  ];

  /** @return BelongsTo<\App\Models\Datacenter, $this> */
  public function datacenter(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Datacenter::class, 'datacenter_id');
  }
}
