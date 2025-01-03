<?php

declare(strict_types=1);

namespace App\Models;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Documentitem extends Common
{
  use PivotEventTrait;

  protected $definition = '\App\Models\Definitions\Documentitem';
  protected $titles = ['Document item', 'Document items'];
  protected $icon = 'file';
  protected $table = 'document_item';
  protected $hasEntityField = false;
}
