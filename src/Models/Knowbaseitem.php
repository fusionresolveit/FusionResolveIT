<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowbaseitem extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Knowbaseitem::class;
  protected $titles = ['Knowledge base item', 'Knowledge base items'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'user',
  ];

  protected $with = [
    'category:id,name,treepath',
    'user:id,name,firstname,lastname',
  ];

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class, 'category_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
