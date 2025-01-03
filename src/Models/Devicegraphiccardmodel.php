<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegraphiccardmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicegraphiccardmodel';
  protected $titles = ['Device graphic card model', 'Device graphic card models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
