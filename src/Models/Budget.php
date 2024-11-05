<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Budget extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Budget';
  protected $titles = ['Budget', 'Budgets'];
  protected $icon = 'calculator';

  protected $appends = [
    'location',
    'type',
    'entity',
    'notes',
  ];

  protected $visible = [
    'location',
    'type',
    'entity',
    'notes',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'entity:id,name',
    'notes:id',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Budgettype', 'budgettype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }
}
