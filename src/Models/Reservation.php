<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Common
{
  protected $definition = '\App\Models\Definitions\Reservation';
  protected $titles = ['Reservation', 'Reservations'];
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
