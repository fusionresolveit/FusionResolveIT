<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documenttype extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Documenttype::class;
  protected $titles = ['Document type', 'Document types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
