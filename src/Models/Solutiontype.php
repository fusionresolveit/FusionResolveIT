<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Solutiontype extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Solutiontype';
  protected $titles = ['Solution type', 'Solution types'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];
}
