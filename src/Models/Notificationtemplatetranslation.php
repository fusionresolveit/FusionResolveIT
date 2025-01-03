<?php

declare(strict_types=1);

namespace App\Models;

class Notificationtemplatetranslation extends Common
{
  protected $definition = '\App\Models\Definitions\Notificationtemplatetranslation';
  protected $titles = ['Notification template translation', 'Notification template translations'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
