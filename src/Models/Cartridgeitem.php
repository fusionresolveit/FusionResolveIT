<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartridgeitem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Cartridgeitem';
  protected $titles = ['Cartridge', 'Cartridges'];
  protected $icon = 'fill drip';

  protected $appends = [
    'type',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
  ];

  protected $visible = [
    'type',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
  ];

  protected $with = [
    'type:id,name',
    'manufacturer:id,name',
    'groupstech:id,name',
    'userstech:id,name',
    'location:id,name',
    'entity:id,name',
  ];


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Cartridgeitemtype', 'cartridgeitemtype_id');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
