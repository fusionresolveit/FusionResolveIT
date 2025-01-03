<?php

declare(strict_types=1);

namespace App\Models;

class ChangeProblem extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeProblem';
  protected $titles = ['Change Problem', 'Change Problem'];
  protected $icon = 'edit';
  protected $table = 'change_problem';
  protected $hasEntityField = false;
}
