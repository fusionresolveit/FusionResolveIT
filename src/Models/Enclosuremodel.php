<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Enclosuremodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Enclosuremodel';
  protected $titles = ['Enclosure model', 'Enclosure models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
