<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Domainrecordtype extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Domainrecordtype';
  protected $titles = ['Record type', 'Records types'];
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
