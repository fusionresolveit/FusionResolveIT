<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devicebatterytype extends Common
{
  protected $definition = '\App\Models\Definitions\Devicebatterytype';
  protected $titles = ['Battery type', 'Battery types'];
  protected $icon = 'edit';
}