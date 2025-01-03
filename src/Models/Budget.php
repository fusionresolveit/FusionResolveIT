<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Budget';
  protected $titles = ['Budget', 'Budgets'];
  protected $icon = 'calculator';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'type',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\Budgettype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budgettype::class, 'budgettype_id');
  }
}
