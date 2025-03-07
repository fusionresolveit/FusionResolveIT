<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Networkname extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Networkname::class;
  protected $titles = ['Network name', 'Network names'];
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'alias',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'fqdn',
    'entity',
    'alias',
  ];

  protected $with = [
    'fqdn:id,name',
    'entity:id,name,completename',
    'alias',
  ];

  /** @return BelongsTo<\App\Models\Fqdn, $this> */
  public function fqdn(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Fqdn::class);
  }

  /** @return HasMany<\App\Models\Networkalias, $this> */
  public function alias(): HasMany
  {
    return $this->hasMany(\App\Models\Networkalias::class, 'networkname_id');
  }
}
