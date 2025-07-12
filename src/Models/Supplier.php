<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Supplier extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Tickets;
  use \App\Traits\Relationships\Problems;
  use \App\Traits\Relationships\Changes;
  use \App\Traits\Relationships\Notes;
  use \App\Traits\Relationships\Knowledgebasearticles;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Supplier::class;
  protected $icon = 'dolly';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'tickets',
    'problems',
    'changes',
    'notes',
    'knowledgebasearticles',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'entity',
    'notes',
    'knowledgebasearticles',
    'documents',
    'tickets',
    'problems',
    'changes',
  ];

  protected $with = [
    'type:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowledgebasearticles:id,name',
    'documents:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Supplier', 'Suppliers', $nb);
  }

  /** @return BelongsTo<\App\Models\Suppliertype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Suppliertype::class, 'suppliertype_id');
  }
}
