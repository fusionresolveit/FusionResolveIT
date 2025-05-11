<?php

declare(strict_types=1);

namespace App\Models\Rules;

class User extends \App\Models\Rules\Rule
{
  protected $titles = ['Rights for users', 'Rights for users'];
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
}
