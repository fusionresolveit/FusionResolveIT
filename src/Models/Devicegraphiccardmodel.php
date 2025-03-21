<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicegraphiccardmodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicegraphiccardmodel::class;
  protected $titles = ['Device graphic card model', 'Device graphic card models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
