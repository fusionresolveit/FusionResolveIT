<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklist extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Blacklist::class;
  protected $titles = ['Blacklist', 'Blacklists'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
