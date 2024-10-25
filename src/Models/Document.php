<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Document';
  protected $titles = ['Document', 'Documents'];
  protected $icon = 'file';

  protected $appends = [
    'categorie',
    'entity',
  ];

  protected $visible = [
    'categorie',
    'entity',
  ];

  protected $with = [
    'categorie:id,name',
    'entity:id,name',
  ];

  public function categorie(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Documentcategory', 'documentcategory_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
