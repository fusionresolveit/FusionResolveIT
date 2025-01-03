<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reservations
{
  /** @return MorphMany<\App\Models\Reservationitem, $this> */
  public function reservations(): MorphMany
  {
    return $this->morphMany(
      \App\Models\Reservationitem::class,
      'item',
    );
  }
}
