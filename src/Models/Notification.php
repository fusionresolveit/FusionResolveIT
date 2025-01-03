<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Notification';
  protected $titles = ['Notification', 'Notifications'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'templates',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];

  /** @return BelongsToMany<\App\Models\Notificationtemplate, $this> */
  public function templates(): BelongsToMany
  {
      return $this->belongsToMany(\App\Models\Notificationtemplate::class)->withPivot('mode');
  }
}
