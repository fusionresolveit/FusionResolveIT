<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\State::class;
  protected $titles = ['Status of items', 'Statuses of items'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'state',
    'entity',
  ];

  protected $with = [
    'state:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }
}
