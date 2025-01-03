<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Notes
{
  /** @return MorphMany<\App\Models\Notepad, $this> */
  public function notes(): MorphMany
  {
    return $this->morphMany(
      \App\Models\Notepad::class,
      'item',
    );
  }
}
