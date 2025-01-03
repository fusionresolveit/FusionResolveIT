<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Usercategory extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Usercategory';
  protected $titles = ['User category', 'User categories'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
