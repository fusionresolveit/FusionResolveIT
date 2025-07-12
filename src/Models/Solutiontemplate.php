<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solutiontemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Solutiontemplate::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'entity',
  ];

  protected $with = [
    'types:id,name',
    'entity:id,name,completename',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Solution template', 'Solution templates', $nb);
  }

  /** @return BelongsTo<\App\Models\Solutiontype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Solutiontype::class, 'solutiontype_id');
  }
}
