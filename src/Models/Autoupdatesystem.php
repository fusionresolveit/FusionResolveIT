<?php

declare(strict_types=1);

namespace App\Models;

class Autoupdatesystem extends Common
{
  protected $definition = '\App\Models\Definitions\Autoupdatesystem';
  protected $titles = ['Update Source', 'Update Sources'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
