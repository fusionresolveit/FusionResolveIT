<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Rulerightparameter extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Rulerightparameter';
  protected $titles = ['LDAP criterion', 'LDAP criteria'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
