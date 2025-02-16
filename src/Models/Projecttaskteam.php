<?php

declare(strict_types=1);

namespace App\Models;

class Projecttaskteam extends Common
{
  protected $definition = \App\Models\Definitions\Projecttaskteam::class;
  protected $titles = ['Project task team', 'Project task teams'];
  protected $icon = 'columns';
  protected $hasEntityField = false;
}
