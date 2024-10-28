<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Home extends Common
{
  protected $definition = '\App\Models\Definitions\Home';
  protected $titles = ['Home', 'Home'];
  protected $icon = 'house';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];
}
