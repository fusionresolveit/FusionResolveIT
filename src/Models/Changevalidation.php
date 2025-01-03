<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changevalidation extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Changevalidation';
  protected $titles = ['Change validation', 'Change validations'];
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
