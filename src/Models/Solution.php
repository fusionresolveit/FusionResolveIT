<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solution extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Solution::class;
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
        $ticket = \App\Models\Ticket::where('id', $model->item_id)->first();
        if (!is_null($ticket))
        {
          $ticket->status = 5;
          $ticket->save();
        }
      }
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('ITIL', 'Solution', 'Solutions', $nb);
  }

  public function getStatusnameAttribute(): string
  {
    switch ($this->attributes['status'])
    {
      case 1:
          return pgettext('ticket solution', 'Not subject to approval');

      case 2:
          return pgettext('ticket solution', 'Waiting for approval');

      case 3:
          return pgettext('ticket solution', 'Granted');

      case 4:
          return pgettext('ticket solution', 'Refused');
    }
    return '';
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
