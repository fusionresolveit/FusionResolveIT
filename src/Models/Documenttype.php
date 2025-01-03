<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Documenttype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Documenttype';
  protected $titles = ['Document type', 'Document types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
