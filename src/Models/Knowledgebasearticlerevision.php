<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowledgebasearticlerevision extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Knowledgebasearticlerevision::class;
  protected $titles = ['Knowledge base article revision', 'Knowledge base article  revision'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
  ];

  protected $with = [
    'user',
  ];

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }
}
