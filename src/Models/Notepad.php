<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notepad extends Common
{
  protected $definition = '\App\Models\Definitions\Notepad';
  protected $titles = ['Notepad', 'Notepads'];
  protected $icon = 'virus slash';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
    'userlastupdater',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'userlastupdater:id,name,firstname,lastname',
  ];

  protected $fillable = [
  ];

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function userlastupdater(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_lastupdater');
  }
}
