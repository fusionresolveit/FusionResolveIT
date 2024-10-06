<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Common
{
  protected $definition = '\App\Models\Definitions\Notification';
  protected $titles = ['Notification', 'Notifications'];
  protected $icon = 'edit';
}