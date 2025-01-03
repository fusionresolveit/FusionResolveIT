<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Contacttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Contacttype';
  protected $titles = ['Contact type', 'Contact types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
