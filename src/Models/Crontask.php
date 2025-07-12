<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Crontask extends Common
{
  use SoftDeletes;
  use CascadesDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Crontask::class;
  protected $icon = 'edit';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'crontaskexecutions',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Automatic action', 'Automatic actions', $nb);
  }

  /** @return HasMany<\App\Models\Crontaskexecution, $this> */
  public function crontaskexecutions(): HasMany
  {
    return $this->hasMany(\App\Models\Crontaskexecution::class)->orderBy('created_at', 'desc');
  }
}
