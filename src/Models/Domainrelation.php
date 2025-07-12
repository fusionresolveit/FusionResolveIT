<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Domainrelation extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Domainrelation::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'domains',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'domains',
  ];

  protected $with = [
    'entity:id,name,completename',
    'domains',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Domain relation', 'Domain relations', $nb);
  }

  /** @return BelongsToMany<\App\Models\Domain, $this> */
  public function domains(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Domain::class, 'domain_item', 'domainrelation_id', 'domain_id');
  }
}
