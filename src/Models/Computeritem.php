<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Computeritem extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Computeritem';
  protected $titles = ['Computer item', 'Computer items'];
  protected $icon = 'laptop';
  protected $table = 'computer_item';
  protected $hasEntityField = false;
}
