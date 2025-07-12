<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticketvalidation extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = \App\Models\Definitions\Ticketvalidation::class;
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'usersrequester',
    'uservalidate',
  ];

  protected $with = [
    'entity:id,name,completename',
    'usersrequester:id,name,firstname,lastname',
    'uservalidate:id,name,firstname,lastname',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Ticket validation', 'Ticket validations', $nb);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function usersrequester(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function uservalidate(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_validate');
  }
}
