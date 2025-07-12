<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rulerightparameter extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Rulerightparameter::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'LDAP criterion', 'LDAP criteria', $nb);
  }
}
