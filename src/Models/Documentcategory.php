<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documentcategory extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Documentcategory';
  protected $titles = ['Document heading', 'Document headings'];
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

  /** @return BelongsTo<\App\Models\Documentcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Documentcategory::class, 'documentcategory_id');
  }
}
