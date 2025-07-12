<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Alert::class;
  protected $icon = 'bell';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    // 'user',
  ];

  protected $with = [
    'entity:id,name,completename',
    // 'user:id,name,firstname,lastname',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('alert title', 'Alert', 'Alerts', $nb);
  }

  // public function user(): BelongsTo
  // {
  //   return $this->belongsTo(\App\Models\User::class, 'users_id');
  // }
}
