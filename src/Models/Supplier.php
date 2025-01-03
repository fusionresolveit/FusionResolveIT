<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Supplier';
  protected $titles = ['Supplier', 'Suppliers'];
  protected $icon = 'dolly';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'tickets',
    'problems',
    'changes',
  ];

  protected $with = [
    'type:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
  ];

  /** @return BelongsTo<\App\Models\Suppliertype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Suppliertype::class, 'suppliertype_id');
  }
}
