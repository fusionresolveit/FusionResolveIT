<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
