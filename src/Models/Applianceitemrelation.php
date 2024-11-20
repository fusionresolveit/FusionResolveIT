<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
