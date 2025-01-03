<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Changes
{
  /** @return MorphToMany<\App\Models\Change, $this> */
  public function changes(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Change::class,
      'item',
      'change_item'
    )->withPivot(
      'change_id',
    );
  }
}
