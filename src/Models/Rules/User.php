<?php

declare(strict_types=1);

namespace App\Models\Rules;

class User extends \App\Models\Rules\Rule
{
  public $definitionCriteria = \App\Models\Definitions\User::class;
  public $definitionActions = null;

  // For default values
  protected $attributes = [
    'sub_type' => 'user',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::addGlobalScope('rightfilter', function ($builder)
    {
      $builder->where('sub_type', 'user');
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Right for users', 'Rights for users', $nb);
  }
}
