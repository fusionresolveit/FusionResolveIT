<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Profile';
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
