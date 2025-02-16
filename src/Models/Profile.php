<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Profile::class;
  protected $titles = ['Profile', 'Profiles'];
  protected $icon = 'user check';
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

    static::deleted(function ($model)
    {
      $profilerights = \App\Models\Profileright::where('profile_id', $model->id)->get();
      foreach ($profilerights as $profileright)
      {
        $profileright->delete();
      }
    });
  }
}
