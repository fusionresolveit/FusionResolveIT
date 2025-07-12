<?php

declare(strict_types=1);

namespace App\Models;

class Crontaskexecutionlog extends Common
{
  protected $definition = \App\Models\Definitions\Crontaskexecutionlog::class;
  protected $icon = 'list ul';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('cron', 'Execution log', 'Execution logs', $nb);
  }
}
