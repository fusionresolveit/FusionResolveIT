<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Knowledgebasearticles
{
  /** @return MorphToMany<\App\Models\Knowledgebasearticle, $this> */
  public function knowledgebasearticles(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Knowledgebasearticle::class,
      'item',
      'knowledgebasearticle_item'
    )->withPivot(
      'knowledgebasearticle_id',
    );
  }
}
