<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Home extends Common
{
  use SoftDeletes;

  protected $definition = \App\Models\Definitions\Home::class;
  protected $titles = ['Home', 'Home'];
  protected $icon = 'house';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];
}
