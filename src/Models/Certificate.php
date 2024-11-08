<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Certificate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Certificate';
  protected $titles = ['Certificate', 'Certificates'];
  protected $icon = 'certificate';

  protected $appends = [
    'location',
    'type',
    'state',
    'user',
    'group',
    'userstech',
    'groupstech',
    'manufacturer',
    'entity',
    'notes',
  ];

  protected $visible = [
    'location',
    'type',
    'state',
    'user',
    'group',
    'userstech',
    'groupstech',
    'manufacturer',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'state:id,name',
    'user:id,name',
    'group:id,name',
    'userstech:id,name',
    'groupstech:id,name',
    'manufacturer:id,name',
    'entity:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Certificatetype', 'certificatetype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State', 'state_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
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

  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Knowbaseitem',
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
    );
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
