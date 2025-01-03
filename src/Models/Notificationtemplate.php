<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Notificationtemplate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Notificationtemplate';
  protected $titles = ['Notification template', 'Notification templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
