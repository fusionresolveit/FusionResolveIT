<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Fqdn extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Fqdn::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'alias',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'alias',
  ];

  protected $with = [
    'entity:id,name,completename',
    'alias',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Internet domain', 'Internet domains', $nb);
  }

  /** @return HasMany<\App\Models\Networkalias, $this> */
  public function alias(): HasMany
  {
    return $this->hasMany(\App\Models\Networkalias::class, 'fqdn_id');
  }
}
