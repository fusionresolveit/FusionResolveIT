<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queuednotification extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Queuednotification::class;
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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Notifications queue', 'Notifications queues', $nb);
  }

  /** @return BelongsTo<\App\Models\Notificationtemplate, $this> */
  public function notificationtemplate(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Notificationtemplate::class);
  }
}
