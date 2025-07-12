<?php

declare(strict_types=1);

namespace App\Models\Rules;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Rule extends \App\Models\Common
{
  use SoftDeletes;
  use CascadesDeletes;

  // need declare here for child class extends this class
  protected $table = 'rules';
  protected $definition = \App\Models\Definitions\Rule::class;
  protected $icon = 'magic';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'criteria',
    'actions',
  ];

  protected $appends = [
  ];

  protected $visible = [
    'criteria',
    'actions',
  ];

  protected $with = [
    'criteria',
    'actions',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Rule', 'Rules', $nb);
  }

  /** @var string|null */
  public $definitionCriteria = null;

  /** @var string|null */
  public $definitionActions = null;

  /** @return HasMany<\App\Models\Rules\Rulecriterium, $this> */
  public function criteria(): HasMany
  {
    return $this->HasMany(\App\Models\Rules\Rulecriterium::class, 'rule_id');
  }

  /** @return HasMany<\App\Models\Rules\Ruleaction, $this> */
  public function actions(): HasMany
  {
    return $this->HasMany(\App\Models\Rules\Ruleaction::class, 'rule_id');
  }
}
