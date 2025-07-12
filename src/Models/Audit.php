<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Audit::class;
  protected $icon = 'scroll';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Audit', 'Audits', $nb);
  }
}
