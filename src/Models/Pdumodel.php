<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Pdumodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Pdumodel';
  protected $titles = ['PDU model', 'PDU models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
