<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fieldblacklist extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Fieldblacklist::class;
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
    return npgettext('global', 'Ignored value for the unicity', 'Ignored values for the unicity', $nb);
  }
}
