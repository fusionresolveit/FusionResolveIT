<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devicegenericmodel extends Common
{
  protected $definition = '\App\Models\Definitions\Devicegenericmodel';
  protected $titles = ['Device generic model', 'Device generic models'];
  protected $icon = 'edit';
}