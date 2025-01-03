<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Certificateitem extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Certificateitem';
  protected $titles = ['Certificate item', 'Certificate items'];
  protected $icon = 'certificate';
  protected $table = 'certificate_item';
  protected $hasEntityField = false;
}
