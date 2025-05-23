<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificationtemplate extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Notificationtemplate::class;
  protected $titles = ['Notification template', 'Notification templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
