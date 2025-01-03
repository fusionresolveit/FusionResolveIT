<?php

declare(strict_types=1);

namespace App\Models;

class Projectteam extends Common
{
  protected $definition = '\App\Models\Definitions\Projectteam';
  protected $titles = ['Project team', 'Project teams'];
  protected $icon = 'columns';
  protected $hasEntityField = false;
}
