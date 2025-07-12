<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractcost extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contractcost::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'budget',
  ];

  protected $with = [
    'budget:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('contract', 'Contract cost', 'Contract costs', $nb);
  }

  /** @return BelongsTo<\App\Models\Budget, $this> */
  public function budget(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Budget::class);
  }
}
