<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Reservationitem extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = \App\Models\Definitions\Reservationitem::class;
  protected $titles = ['Reservation item', 'Reservation items'];
  protected $icon = 'edit';
  protected $table = 'reservationitems';
  /** @var string[] */
  protected $cascadeDeletes = [
    'reservations',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'reservations',
  ];

  protected $with = [
    'entity:id,name,completename',
    'reservations',
  ];

  /** @return HasMany<\App\Models\Reservation, $this> */
  public function reservations(): HasMany
  {
    return $this->hasMany(\App\Models\Reservation::class);
  }
}
