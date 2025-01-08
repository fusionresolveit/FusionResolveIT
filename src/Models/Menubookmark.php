<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menubookmark extends Common
{
  public $timestamps = false;
  protected $definition = '\App\Models\Definitions\Menubookmark';
  protected $titles = ['Menu bookmark', 'Menu bookmarks'];
  protected $icon = 'bookmark';

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
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }
}
