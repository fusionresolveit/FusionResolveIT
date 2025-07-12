<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;

class Networkalias extends Common
{
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Networkalias::class;
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
    return npgettext('network', 'Network alias', 'Network aliases', $nb);
  }
}
