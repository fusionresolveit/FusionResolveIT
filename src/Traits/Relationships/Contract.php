<?php

declare(strict_types=1);

namespace App\Traits\Relationships;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Contract
{
  /** @return MorphToMany<\App\Models\Contract, $this> */
  public function contracts(): MorphToMany
  {
    return $this->morphToMany(
      \App\Models\Contract::class,
      'item',
      'contract_item'
    )->withPivot(
      'contract_id',
    );
  }
}
