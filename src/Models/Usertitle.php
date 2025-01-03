<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Usertitle extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Usertitle';
  protected $titles = ['User title', 'Users titles'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
