<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Projecttask extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Projecttask::class;
  protected $titles = ['Project task', 'Project tasks'];
  protected $icon = 'columns';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'notes',
    'tickets',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'parent',
    'type',
    'state',
    'tickets',
    'notes',
    'documents',
    'project',
  ];

  protected $with = [
    'entity:id,name,completename',
    'parent:id,name',
    'type:id,name',
    'state:id,name,color',
    'tickets:id,name',
    'notes:id',
    'documents:id,name',
    'project:id,name',
  ];

  /** @return BelongsTo<\App\Models\Projecttask, $this> */
  public function parent(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttask::class, 'projecttask_id');
  }

  /** @return BelongsTo<\App\Models\Projecttasktype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttasktype::class, 'projecttasktype_id');
  }

  /** @return BelongsTo<\App\Models\Projectstate, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projectstate::class, 'projectstate_id');
  }

  /** @return BelongsToMany<\App\Models\Ticket, $this> */
  public function tickets(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Ticket::class);
  }

  /** @return BelongsTo<\App\Models\Project, $this> */
  public function project(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Project::class);
  }
}
