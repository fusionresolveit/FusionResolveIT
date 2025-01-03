<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Problems
{
  /** @return MorphToMany<\App\Models\Problem, $this> */
  public function problems(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Problem::class,
      'item',
      'item_problem'
    )->withPivot(
      'problem_id',
    );
  }
}
