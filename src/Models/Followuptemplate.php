<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followuptemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Followuptemplate::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'source',
    'entity',
  ];

  protected $with = [
    'source:id,name',
    'entity:id,name,completename',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Followup template', 'Followup templates', $nb);
  }

  /** @return BelongsTo<\App\Models\Requesttype, $this> */
  public function source(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Requesttype::class, 'requesttype_id');
  }
}
