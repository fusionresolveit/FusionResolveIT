<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rssfeed extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Rssfeed';
  protected $titles = ['RSS feed', 'RSS feed'];
  protected $icon = 'rss';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
  ];


  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
