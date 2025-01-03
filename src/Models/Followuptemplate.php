<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followuptemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Followuptemplate';
  protected $titles = ['Followup template', 'Followup templates'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'source',
    'entity',
  ];

  protected $with = [
    'source:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Requesttype, $this> */
  public function source(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Requesttype::class, 'requesttype_id');
  }
}
