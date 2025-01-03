<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queuednotification extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Queuednotification';
  protected $titles = ['Notification queue', 'Notification queue'];
  protected $icon = 'list alt';

  protected $appends = [
  ];

  protected $visible = [
    'notificationtemplate',
    'entity',
  ];

  protected $with = [
    'notificationtemplate:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Notificationtemplate, $this> */
  public function notificationtemplate(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Notificationtemplate::class);
  }
}
