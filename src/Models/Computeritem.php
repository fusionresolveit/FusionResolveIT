<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
