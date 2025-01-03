<?php

declare(strict_types=1);

namespace App\Models;

class ChangeItem extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeItem';
  protected $titles = ['Change Item', 'Change Items'];
  protected $icon = 'edit';
  protected $table = 'change_item';
  protected $hasEntityField = false;
}
