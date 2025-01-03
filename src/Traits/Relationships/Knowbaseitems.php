<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Knowbaseitems
{
  /** @return MorphToMany<\App\Models\Knowbaseitem, $this> */
  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Knowbaseitem::class,
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
    );
  }
}
