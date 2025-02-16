<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDevicebattery extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = \App\Models\Definitions\ItemDevicebattery::class;
  protected $titles = ['Devicebattery Item', 'Devicebattery Items'];
  protected $icon = 'edit';
  protected $table = 'item_devicebattery';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'documents',
  ];

  protected $with = [
    'entity:id,name,completename',
    'documents',
  ];
}
