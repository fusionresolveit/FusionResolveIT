<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticketcost extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Ticketcost::class;
  protected $titles = ['Ticket cost', 'Ticket costs'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'budget',
  ];

  protected $with = [
    'entity:id,name,completename',
    'budget:id,name',
  ];


  /** @return BelongsTo<\App\Models\Budget, $this> */
  public function budget(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budget::class);
  }
}
