<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDevicememory extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  protected $definition = '\App\Models\Definitions\ItemDevicememory';
  protected $titles = ['Devicememory Item', 'Devicememory Items'];
  protected $icon = 'edit';
  protected $table = 'item_devicememory';

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
