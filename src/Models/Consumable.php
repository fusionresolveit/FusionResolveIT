<?php

declare(strict_types=1);

namespace App\Models;

class Consumable extends Common
{
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Consumable';
  protected $titles = ['Consumable', 'Consumables'];
  protected $icon = 'box open';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'date_in',
    'date_out',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];
}
