<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSoftwareversion extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\ItemSoftwareversion';
  protected $titles = ['Softwareversion Item', 'Softwareversion Items'];
  protected $icon = 'edit';
  protected $table = 'item_softwareversion';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];
}
