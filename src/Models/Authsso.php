<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authsso extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Authsso::class;
  protected $icon = 'id card alternate';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'scopes',
  ];

  protected $with = [
    'scopes',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::creating(function ($model)
    {
      $model->callbackid = uniqid();
    });

    static::created(function ($model)
    {
      \App\v1\Controllers\Authsso::initScopesForProvider($model);
      \App\v1\Controllers\Authsso::initOptionsForProvider($model);
    });
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('sso', 'SSO', 'SSOs', $nb);
  }

  /** @return HasMany<\App\Models\Authssoscope, $this> */
  public function scopes(): HasMany
  {
    return $this->HasMany(\App\Models\Authssoscope::class);
  }
}
