<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Location extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Location';
  protected $titles = ['Location', 'Locations'];
  protected $icon = 'edit';

  protected $appends = [
    'location',
    'entity',
  ];

  protected $visible = [
    'location',
    'entity',
    'documents',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name',
    'documents:id,name',
  ];

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
}
