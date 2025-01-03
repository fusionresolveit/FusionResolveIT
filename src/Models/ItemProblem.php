<?php

declare(strict_types=1);

namespace App\Models;

class ItemProblem extends Common
{
  protected $definition = '\App\Models\Definitions\ItemProblem';
  protected $titles = ['Problem Item', 'Problem Items'];
  protected $icon = 'edit';
  protected $table = 'item_problem';
  protected $hasEntityField = false;
}
