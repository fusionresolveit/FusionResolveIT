<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menubookmark extends Common
{
  public $timestamps = false;
  protected $definition = \App\Models\Definitions\Menubookmark::class;
  protected $icon = 'bookmark';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Menu bookmark', 'Menu bookmarks', $nb);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }
}
