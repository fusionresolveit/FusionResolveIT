<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followup extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Followup::class;
  protected $titles = ['Followup', 'Followups'];
  protected $icon = 'hands helping';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
    'id',
    'content',
  ];

  protected $with = [
    'user',
  ];

  protected $casts = [
    'is_private' => 'boolean',
    'is_tech'    => 'boolean',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::created(function ($model)
    {
      if ($model->item_type == 'App\Models\Ticket')
      {
        $ticket = \App\Models\Ticket::where('id', $model->item_id)->first();
        if (is_null($ticket))
        {
          throw new \Exception('Ticket not found', 400);
        }
        if ($ticket->status == 1)
        {
          $ticket->status = 2;
          $ticket->save();
        }
      }
    });
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
