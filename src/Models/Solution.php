<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solution extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Solution';
  protected $titles = ['Solution', 'Solutions'];
  protected $icon = 'hands helping';
  protected $hasEntityField = false;

  protected $appends = [
    'statusname',
  ];

  protected $visible = [
    'user',
    'id',
    'statusname',
  ];

  protected $with = [
    'user',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::created(function (\App\Models\Solution $model)
    {
      if ($model->item_type == 'App\Models\Ticket')
      {
        /** @var \App\Models\Ticket|null */
        $ticket = \App\Models\Ticket::find($model->item_id);
        if (!is_null($ticket))
        {
          $ticket->status = 5;
          $ticket->save();
        }
      }
    });
  }

  public function getStatusnameAttribute(): string
  {
    global $translator;
    switch ($this->attributes['status'])
    {
      case 1:
          return $translator->translate('Not subject to approval');

      case 2:
          return $translator->translate('Waiting for approval');

      case 3:
          return $translator->translate('Granted');

      case 4:
          return $translator->translate('Refused');
    }
    return '';
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
