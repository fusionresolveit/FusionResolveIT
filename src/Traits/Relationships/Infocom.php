<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Infocom
{
  /** @return MorphMany<\App\Models\Infocom, $this> */
  public function infocom(): MorphMany
  {
    return $this->morphMany(
      \App\Models\Infocom::class,
      'item',
    );
  }
}
