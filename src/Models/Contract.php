<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contract extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Contract';
  protected $titles = ['Contract', 'Contracts'];
  protected $icon = 'file signature';

  protected $appends = [
    'type',
    'state',
    'entity',
    'notes',
  ];

  protected $visible = [
    'type',
    'state',
    'entity',
    'notes',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'entity:id,name',
    'notes:id',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Contracttype', 'contracttype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
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
