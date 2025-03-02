<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documentcategory extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Documentcategory::class;
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
