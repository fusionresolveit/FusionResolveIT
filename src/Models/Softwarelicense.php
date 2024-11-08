<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Softwarelicense extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Softwarelicense';
  protected $titles = ['License', 'Licenses'];
  protected $icon = 'key';

  protected $appends = [
    'location',
    'softwarelicensetype',
    'userstech',
    'groupstech',
    'user',
    'group',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer',
    'software',
    'entity',
    'certificates',
    'notes',
  ];

  protected $visible = [
    'location',
    'softwarelicensetype',
    'userstech',
    'groupstech',
    'user',
    'group',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer',
    'software',
    'entity',
    'certificates',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
  ];

  protected $with = [
    'location:id,name',
    'softwarelicensetype:id,name',
    'userstech:id,name',
    'groupstech:id,name',
    'user:id,name',
    'group:id,name',
    'state:id,name',
    'softwareversionsBuy:id,name',
    'softwareversionsUse:id,name',
    'manufacturer:id,name',
    'software:id,name',
    'entity:id,name',
    'certificates:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function softwarelicensetype(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwarelicensetype');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function softwareversionsBuy(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwareversion', 'softwareversion_id_buy');
  }

  public function softwareversionsUse(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwareversion', 'softwareversion_id_use');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function software(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Software');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Certificate',
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
    );
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
