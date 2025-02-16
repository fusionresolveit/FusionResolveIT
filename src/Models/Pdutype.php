<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pdutype extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Pdutype::class;
  protected $titles = ['PDU type', 'PDU types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
