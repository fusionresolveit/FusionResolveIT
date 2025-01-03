<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecontrolmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecontrolmodel';
  protected $titles = ['Device control model', 'Device control models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
