<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solutiontemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Solutiontemplate';
  protected $titles = ['Solution template', 'Solution templates'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'types',
    'entity',
  ];

  protected $with = [
    'types:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Solutiontype, $this> */
  public function types(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Solutiontype::class, 'solutiontype_id');
  }
}
