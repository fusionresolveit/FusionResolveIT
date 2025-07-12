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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Reservation item', 'Reservation items', $nb);
  }

  /** @return HasMany<\App\Models\Reservation, $this> */
  public function reservations(): HasMany
  {
    return $this->hasMany(\App\Models\Reservation::class);
  }
}
