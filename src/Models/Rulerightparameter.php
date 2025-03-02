<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rulerightparameter extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Rulerightparameter::class;
  protected $titles = ['LDAP criterion', 'LDAP criteria'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
