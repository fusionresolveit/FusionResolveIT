<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Tickets
{
  /** @return MorphToMany<\App\Models\Ticket, $this> */
  public function tickets(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Ticket::class,
      'item',
      'item_ticket'
    )->withPivot(
      'ticket_id',
    );
  }
}
