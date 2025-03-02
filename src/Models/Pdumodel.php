<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pdumodel extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Pdumodel::class;
  protected $titles = ['PDU model', 'PDU models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
