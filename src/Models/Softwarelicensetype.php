<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Softwarelicensetype extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Softwarelicensetype::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'softwarelicensetype',
    'entity',
  ];

  protected $with = [
    'softwarelicensetype:id,name',
    'entity:id,name,completename',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'License type', 'License types', $nb);
  }

  /** @return BelongsTo<\App\Models\Softwarelicensetype, $this> */
  public function softwarelicensetype(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwarelicensetype::class);
  }
}
