<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Location
{
  /** @return BelongsTo<\App\Models\Location, $this> */
  public function location(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Location::class);
  }
}
