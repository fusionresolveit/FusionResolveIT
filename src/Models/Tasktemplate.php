<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tasktemplate extends Common
{
  protected $definition = '\App\Models\Definitions\Tasktemplate';
  protected $titles = ['Task template', 'Task templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'users',
    'groups',
  ];

  protected $with = [
    'category:id,name',
    'users:id,name,firstname,lastname',
    'groups:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Taskcategory, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Taskcategory::class);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function users(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class, 'user_id_tech');
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function groups(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class, 'group_id_tech');
  }
}
