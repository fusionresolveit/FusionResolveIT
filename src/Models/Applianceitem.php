<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Applianceitem extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Applianceitem';
  protected $titles = ['Appliance item', 'Appliance items'];
  protected $icon = 'cubes';
  protected $table = 'appliance_item';
  protected $hasEntityField = false;
}
