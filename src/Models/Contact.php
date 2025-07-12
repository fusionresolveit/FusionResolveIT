<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Contact extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;
  use \App\Traits\Relationships\Notes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Contact::class;
  protected $icon = 'user tie';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'notes',
    'suppliers',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'type',
    'title',
    'entity',
    'notes',
    'documents',
    'suppliers',
  ];

  protected $with = [
    'type:id,name',
    'title:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
    'suppliers:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Contact', 'Contacts', $nb);
  }

  /** @return BelongsTo<\App\Models\Contacttype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Contacttype::class, 'contacttype_id');
  }

  /** @return BelongsTo<\App\Models\Usertitle, $this> */
  public function title(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Usertitle::class, 'usertitle_id');
  }

  /** @return BelongsToMany<\App\Models\Supplier, $this> */
  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Supplier::class);
  }
}
