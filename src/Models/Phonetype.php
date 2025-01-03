<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Phonetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Phonetype';
  protected $titles = ['Phone type', 'Phone types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
