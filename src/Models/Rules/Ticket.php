<?php

namespace App\Models\Rules;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends \App\Models\Common
{
  protected $table = 'rules';
  protected $definition = '\App\Models\Definitions\Rule';
  protected $titles = ['Business rules for tickets', 'Business rules for tickets'];
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

  // For default values
  protected $attributes = [
    'sub_type' => 'RuleTicket',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::addGlobalScope('ticketfilter', function (Builder $builder)
    {
      $builder->where('sub_type', 'RuleTicket');
    });
  }

  public function criteria(): HasMany
  {
    return $this->HasMany('\App\Models\Rules\Rulecriterium', 'rule_id');
  }

  public function actions(): HasMany
  {
    return $this->HasMany('\App\Models\Rules\Ruleaction', 'rule_id');
  }
}
