<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Contracttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Contracttype';
  protected $titles = ['Contract type', 'Contract types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
