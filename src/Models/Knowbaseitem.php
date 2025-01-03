<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowbaseitem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Knowbaseitem';
  protected $titles = ['Knowledge base item', 'Knowledge base items'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'user',
  ];

  protected $with = [
    'category:id,name',
    'user:id,name,firstname,lastname',
  ];

  /** @return BelongsTo<\App\Models\Knowbaseitemcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Knowbaseitemcategory::class, 'knowbaseitemcategory_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
