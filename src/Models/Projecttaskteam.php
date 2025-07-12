<?php

declare(strict_types=1);

namespace App\Models;

class Projecttaskteam extends Common
{
  protected $definition = \App\Models\Definitions\Projecttaskteam::class;
  protected $icon = 'columns';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('project', 'Project task team', 'Project task teams', $nb);
  }
}
