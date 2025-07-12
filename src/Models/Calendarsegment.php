<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Calendarsegment extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = \App\Models\Definitions\Calendarsegment::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Calendar segment', 'Calendar segments', $nb);
  }
}
