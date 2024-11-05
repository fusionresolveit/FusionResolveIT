<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Project';
  protected $titles = ['Project', 'Projects'];
  protected $icon = 'columns';

  protected $appends = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
  ];

  protected $visible = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'user:id,name',
    'group:id,name',
    'entity:id,name',
    'notes:id',
  ];


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttype', 'projecttype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projectstate', 'projectstate_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
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
