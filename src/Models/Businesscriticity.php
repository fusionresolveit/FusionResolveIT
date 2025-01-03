<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Businesscriticity extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Businesscriticity';
  protected $titles = ['Business criticity', 'Business criticities'];
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

  /** @return BelongsTo<\App\Models\Businesscriticity, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Businesscriticity::class, 'businesscriticity_id');
  }
}
