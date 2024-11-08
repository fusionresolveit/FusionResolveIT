<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Supplier';
  protected $titles = ['Supplier', 'Suppliers'];
  protected $icon = 'dolly';

  protected $appends = [
    'type',
    'entity',
    'notes',
  ];

  protected $visible = [
    'type',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
  ];

  protected $with = [
    'type:id,name',
    'entity:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Suppliertype', 'suppliertype_id');
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
}
