<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDevicenetworkcard extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\ItemDevicenetworkcard';
  protected $titles = ['Devicenetworkcard Item', 'Devicenetworkcard Items'];
  protected $icon = 'edit';
  protected $table = 'item_devicenetworkcard';

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
