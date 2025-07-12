<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notepad extends Common
{
  protected $definition = \App\Models\Definitions\Notepad::class;
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Notepad', 'Notepads', $nb);
  }

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
