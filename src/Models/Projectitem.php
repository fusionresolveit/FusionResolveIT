<?php

declare(strict_types=1);

namespace App\Models;

class Projectitem extends Common
{
  protected $definition = '\App\Models\Definitions\Projectitem';
  protected $titles = ['Project item', 'Project items'];
  protected $icon = 'columns';
  protected $table = 'item_project';
  protected $hasEntityField = false;
}
