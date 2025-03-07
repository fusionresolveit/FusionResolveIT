<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Dcroom extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Infocom;
  use \App\Traits\Relationships\Contract;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Dcroom::class;
  protected $titles = ['Server room', 'Server rooms'];
  protected $icon = 'warehouse';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'contracts',
  ];

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
