<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domainrecord extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Domainrecord::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'type',
  ];

  protected $with = [
    'entity:id,name,completename',
    'type:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('inventory device', 'Record', 'Records', $nb);
  }

  /** @return BelongsTo<\App\Models\Domainrecordtype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Domainrecordtype::class, 'domainrecordtype_id');
  }
}
