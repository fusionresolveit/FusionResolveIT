<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplatemandatoryfields extends Common
{
  protected $definition = \App\Models\Definitions\Tickettemplatemandatoryfields::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Ticket template', 'Ticket templates', $nb);
  }
}
