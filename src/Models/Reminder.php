<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Reminder';
  protected $titles = ['Reminder', 'Reminders'];
  protected $icon = 'sticky note';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
    'documents',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
