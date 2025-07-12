<?php

declare(strict_types=1);

namespace App\Models;

class Crontaskexecution extends Common
{
  protected $definition = \App\Models\Definitions\Crontaskexecution::class;
  protected $icon = 'cogs';
  protected $hasEntityField = false;

  public const STATE_START = 0;
  public const STATE_RUN   = 1;
  public const STATE_STOP  = 2;
  public const STATE_ERROR = 3;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('cron', 'Execution', 'Executions', $nb);
  }
}
