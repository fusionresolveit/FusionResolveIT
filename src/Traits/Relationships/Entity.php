<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Entity
{
  /** @return BelongsTo<\App\Models\Entity, $this> */
  public function entity(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Entity::class);
  }
}
