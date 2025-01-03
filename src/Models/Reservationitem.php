<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservationitem extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Reservationitem';
  protected $titles = ['Reservation item', 'Reservation items'];
  protected $icon = 'edit';
  protected $table = 'reservationitems';

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
