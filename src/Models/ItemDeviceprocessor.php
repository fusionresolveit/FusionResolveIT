<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDeviceprocessor extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\ItemDeviceprocessor';
  protected $titles = ['Deviceprocessor Item', 'Deviceprocessor Items'];
  protected $icon = 'edit';
  protected $table = 'item_deviceprocessor';

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
