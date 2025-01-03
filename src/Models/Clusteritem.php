<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Clusteritem extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Clusteritem';
  protected $titles = ['Cluster item', 'Cluster items'];
  protected $icon = 'project diagram';
  protected $table = 'item_cluster';
  protected $hasEntityField = false;
}
