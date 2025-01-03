<?php

declare(strict_types=1);

namespace App\Models;

class Crontaskexecutionlog extends Common
{
  protected $definition = '\App\Models\Definitions\Crontaskexecutionlog';
  protected $titles = ['Execution log', 'Execution logs'];
  protected $icon = 'list ul';
  protected $hasEntityField = false;
}
