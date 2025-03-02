<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mailcollector extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Mailcollector::class;
  protected $titles = ['Receiver', 'Receivers'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
