<?php

declare(strict_types=1);

namespace App\Models;

class ItemEnclosure extends Common
{
  protected $definition = '\App\Models\Definitions\ItemEnclosure';
  protected $titles = ['Enclosure Item', 'Enclosure Items'];
  protected $icon = 'edit';
  protected $table = 'item_enclosure';
  protected $hasEntityField = false;
}
