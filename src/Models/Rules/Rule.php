<?php

declare(strict_types=1);

namespace App\Models\Rules;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends \App\Models\Common
{
  protected $table = 'rules';
  protected $definition = \App\Models\Definitions\Rule::class;
  protected $titles = ['Rule', 'Rules'];
  protected $icon = 'magic';
  protected $hasEntityField = false;

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
