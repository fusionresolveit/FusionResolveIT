<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Devicenetworkcardmodel extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicenetworkcardmodel';
  protected $titles = ['Network card model', 'Network card models'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
