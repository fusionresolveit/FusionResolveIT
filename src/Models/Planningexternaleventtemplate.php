<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planningexternaleventtemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Planningexternaleventtemplate';
  protected $titles = ['External events template', 'External events templates'];
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

  /** @return BelongsTo<\App\Models\Planningeventcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Planningeventcategory::class, 'planningeventcategory_id');
  }
}
