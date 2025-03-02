<?php

declare(strict_types=1);

namespace App\Models;

class ItemTicket extends Common
{
  protected $definition = \App\Models\Definitions\ItemTicket::class;
  protected $titles = ['Ticket Item', 'Ticket Items'];
  protected $icon = 'edit';
  protected $table = 'item_ticket';
  protected $hasEntityField = false;
}
