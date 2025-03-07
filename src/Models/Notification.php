<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Notification extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Notification::class;
  protected $titles = ['Notification', 'Notifications'];
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'templates',
  ];

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
