<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSoftwarelicence extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\ItemSoftwarelicence';
  protected $titles = ['Softwarelicence Item', 'Softwarelicence Items'];
  protected $icon = 'edit';
  protected $table = 'item_softwarelicense';
  protected $hasEntityField = false;
}
