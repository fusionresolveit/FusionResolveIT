<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Project extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Contract;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Project::class;
  protected $titles = ['Project', 'Projects'];
  protected $icon = 'columns';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'contracts',
    'notes',
    'knowledgebasearticles',
    'tasks',
    'parents',
    'costs',
    'itemComputers',
    'itemMonitors',
    'itemNetworkequipments',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
    'itemSoftwares',
    'itilTickets',
    'itilProblems',
    'itilChanges',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'knowledgebasearticles',
    'documents',
    'contracts',
    'tasks',
    'costs',
    'parents',
  ];

  protected $with = [
    'type:id,name',
    'state',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tasks',
    'parents:id,name',
    'costs:id,name',
  ];


  /** @return BelongsTo<\App\Models\Projecttype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projecttype::class, 'projecttype_id');
  }

  /** @return BelongsTo<\App\Models\Projectstate, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Projectstate::class, 'projectstate_id');
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }

  /** @return BelongsTo<\App\Models\Group, $this> */
  public function group(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Group::class);
  }

  /** @return HasMany<\App\Models\Projecttask, $this> */
  public function tasks(): HasMany
  {
    return $this->hasMany(\App\Models\Projecttask::class, 'project_id');
  }

  /** @return HasMany<\App\Models\Project, $this> */
  public function parents(): HasMany
  {
    return $this->hasMany(\App\Models\Project::class, 'project_id');
  }

  /** @return HasMany<\App\Models\Projectcost, $this> */
  public function costs(): HasMany
  {
    return $this->hasMany(\App\Models\Projectcost::class);
  }

  /** @return MorphToMany<\App\Models\Computer, $this> */
  public function itemComputers(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Computer::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Monitor, $this> */
  public function itemMonitors(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Monitor::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Networkequipment::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Peripheral::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Phone, $this> */
  public function itemPhones(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Phone::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Printer, $this> */
  public function itemPrinters(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Printer::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Software, $this> */
  public function itemSoftwares(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Software::class, 'item', 'item_project');
  }

  /** @return MorphToMany<\App\Models\Ticket, $this> */
  public function itilTickets(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Ticket::class, 'item', 'itil_project');
  }

  /** @return MorphToMany<\App\Models\Problem, $this> */
  public function itilProblems(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Problem::class, 'item', 'itil_project');
  }

  /** @return MorphToMany<\App\Models\Change, $this> */
  public function itilChanges(): MorphToMany
  {
    return $this->morphedByMany(\App\Models\Change::class, 'item', 'itil_project');
  }
}
