<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contract extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowbaseitems;

  protected $definition = '\App\Models\Definitions\Contract';
  protected $titles = ['Contract', 'Contracts'];
  protected $icon = 'file signature';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'state',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'suppliers',
    'costs',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'suppliers:id,name',
    'costs:id,name',
  ];

  /** @return BelongsTo<\App\Models\Contracttype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Contracttype::class, 'contracttype_id');
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }

  /** @return BelongsToMany<\App\Models\Supplier, $this> */
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Supplier::class);
  }

  /** @return HasMany<\App\Models\Contractcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Contractcost::class);
  }
}
