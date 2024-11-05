<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Group extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Group';
  protected $titles = ['Group', 'Groups'];
  protected $icon = 'users';

  protected $appends = [
    'entity',
    'notes',
  ];

  protected $visible = [
    'entity',
    'notes',
  ];

  protected $with = [
    'entity:id,name',
    'notes:id',
  ];

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
