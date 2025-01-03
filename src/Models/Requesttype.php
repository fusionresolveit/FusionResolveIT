<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Requesttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Requesttype';
  protected $titles = ['Request source', 'Request sources'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
