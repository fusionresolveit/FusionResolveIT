<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crontask extends Common
{
  protected $definition = '\App\Models\Definitions\Crontask';
  protected $titles = ['Automatic action', 'Automatic actions'];
  protected $icon = 'edit';
}