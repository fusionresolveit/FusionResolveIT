<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemversion extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Operatingsystemversion::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $casts = [
    'is_lts' => 'boolean',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Version of the operating system', 'Version of the operating systems', $nb);
  }
}
