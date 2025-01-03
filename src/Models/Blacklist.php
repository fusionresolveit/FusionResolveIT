<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Blacklist extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Blacklist';
  protected $titles = ['Blacklist', 'Blacklists'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
