<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Projecttasktype extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Projecttasktype::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Project tasks type', 'Project tasks types', $nb);
  }
}
