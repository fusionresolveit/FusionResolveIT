<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Savedsearch extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Savedsearch';
  protected $titles = ['Saved search', 'Saved searches'];
  protected $icon = 'bookmark';

  protected $appends = [
  ];

  protected $visible = [
    'user',
    'entity',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'entity:id,name,completename',
  ];


  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
