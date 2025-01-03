<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Netpoint extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;

  protected $definition = '\App\Models\Definitions\Netpoint';
  protected $titles = ['Network outlet', 'Network outlets'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'entity',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
  ];
}
