<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowbaseitemcategory extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Knowbaseitemcategory';
  protected $titles = ['Knowledge base category', 'Knowledge base categories'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'entity',
  ];

  protected $with = [
    'category:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Knowbaseitemcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Knowbaseitemcategory::class, 'knowbaseitemcategory_id');
  }
}
