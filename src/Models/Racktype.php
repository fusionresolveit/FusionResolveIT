<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racktype extends Common
{
  protected $definition = '\App\Models\Definitions\Racktype';
  protected $titles = ['Rack type', 'Rack types'];
  protected $icon = 'edit';
}