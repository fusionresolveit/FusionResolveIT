<?php

declare(strict_types=1);

namespace App\Models;

class Networkalias extends Common
{
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Networkalias';
  protected $titles = ['Network alias', 'Network aliases'];
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
