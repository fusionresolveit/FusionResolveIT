<?php

declare(strict_types=1);

namespace App\Models;

class Projectteam extends Common
{
  protected $definition = \App\Models\Definitions\Projectteam::class;
  protected $icon = 'columns';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('project', 'Project team', 'Project teams', $nb);
  }
}
