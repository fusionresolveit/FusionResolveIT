<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authsso extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Authsso::class;
  protected $titles = ['SSO', 'SSO'];
  protected $icon = 'id card alternate';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
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
}
