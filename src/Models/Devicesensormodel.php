<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicesensormodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Devicesensormodel::class;
  protected $titles = ['Device sensor model', 'Device sensor models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
