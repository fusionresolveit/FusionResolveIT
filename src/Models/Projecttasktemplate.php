<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projecttasktemplate extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\Projecttasktemplate';
  protected $titles = ['Project task template', 'Project task templates'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'state',
    'type',
    'projecttasks',
    'entity',
    'documents',
  ];

  protected $with = [
    'state:id,name',
    'type:id,name',
    'projecttasks:id,name',
    'entity:id,name,completename',
    'documents:id,name',
  ];

  /** @return BelongsTo<\App\Models\Projectstate, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projectstate::class, 'projectstate_id');
  }

  /** @return BelongsTo<\App\Models\Projecttasktype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttasktype::class, 'projecttasktype_id');
  }

  /** @return BelongsTo<\App\Models\Projecttask, $this> */
  public function projecttasks(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttask::class, 'projecttask_id');
  }
}
