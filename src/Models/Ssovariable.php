<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ssovariable extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Ssovariable::class;
  protected $titles = [
    'Field storage of the login in the HTTP request',
    'Fields storage of the login in the HTTP request'
  ];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
