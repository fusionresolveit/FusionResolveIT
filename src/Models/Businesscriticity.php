<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Businesscriticity extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Businesscriticity::class;
  protected $icon = 'edit';
  protected $tree = true;

  protected $appends = [
  ];

  protected $visible = [
    'businesscriticity',
    'entity',
  ];

  protected $with = [
    'businesscriticity:id,name',
    'entity:id,name,completename',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Business criticity', 'Business criticities', $nb);
  }

  /** @return BelongsTo<\App\Models\Businesscriticity, $this> */
  public function businesscriticity(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Businesscriticity::class, 'businesscriticity_id');
  }
}
