<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Certificatetype extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Certificatetype';
  protected $titles = ['Certificate type', 'Certificate types'];
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
