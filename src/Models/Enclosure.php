<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Enclosure extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Enclosure';
  protected $titles = ['Enclosure', 'Enclosures'];
  protected $icon = 'th';

  protected $appends = [
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
  ];

  protected $visible = [
    'model',
    'state',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'documents',
    'contracts',
  ];

  protected $with = [
    'model:id,name',
    'state:id,name',
    'manufacturer:id,name',
    'groupstech:id,name',
    'userstech:id,name',
    'location:id,name',
    'entity:id,name',
    'documents:id,name',
    'contracts:id,name',
  ];

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Enclosuremodel', 'enclosuremodel_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
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

  public function documents(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Document',
      'item',
      'document_item'
    )->withPivot(
      'document_id',
      'updated_at',
    );
  }

  public function contracts(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Contract',
      'item',
      'contract_item'
    )->withPivot(
      'contract_id',
    );
  }
}
