<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;

  protected $definition = '\App\Models\Definitions\Document';
  protected $titles = ['Document', 'Documents'];
  protected $icon = 'file';

  protected $appends = [
  ];

  protected $visible = [
    'categorie',
    'entity',
    'notes',
    'documents',
  ];

  protected $with = [
    'categorie:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\Documentcategory, $this> */
  public function categorie(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Documentcategory::class, 'documentcategory_id');
  }
}
