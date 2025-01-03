<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Documents
{
  /** @return MorphToMany<\App\Models\Document, $this> */
  public function documents(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Document::class,
      'item',
      'document_item'
    )->withPivot(
      'document_id',
      'updated_at',
    );
  }
}
