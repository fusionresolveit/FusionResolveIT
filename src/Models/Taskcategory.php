<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Taskcategory extends Common
{
  protected $definition = '\App\Models\Definitions\Taskcategory';
  protected $titles = ['Task category', 'Task categories'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'knowbaseitemcategories',
  ];

  protected $with = [
    'category:id,name',
    'knowbaseitemcategories:id,name',
  ];

  /** @return BelongsTo<\App\Models\Taskcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Taskcategory::class);
  }

  /** @return BelongsTo<\App\Models\Knowbaseitemcategory, $this> */
  public function knowbaseitemcategories(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Knowbaseitemcategory::class);
  }
}
