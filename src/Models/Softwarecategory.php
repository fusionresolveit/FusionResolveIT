<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Softwarecategory extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Softwarecategory';
  protected $titles = ['Software category', 'Software categories'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'category',
  ];

  protected $with = [
    'category:id,name',
  ];

  /** @return BelongsTo<\App\Models\Softwarecategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Softwarecategory::class, 'softwarecategory_id');
  }
}
