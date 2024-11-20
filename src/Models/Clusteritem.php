<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
