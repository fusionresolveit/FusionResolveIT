<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDevicecase extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\ItemDevicecase';
  protected $titles = ['Devicecase Item', 'Devicecase Items'];
  protected $icon = 'edit';
  protected $table = 'item_devicecase';

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
