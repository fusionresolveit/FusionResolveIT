<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Applianceitemrelation extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Applianceitemrelation';
  protected $titles = ['Appliance item relation', 'Appliance item relations'];
  protected $icon = 'cubes';
  protected $table = 'appliance_item_relation';
  protected $hasEntityField = false;
}
