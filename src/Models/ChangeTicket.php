<?php

declare(strict_types=1);

namespace App\Models;

class ChangeTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeTicket';
  protected $titles = ['Change Ticket', 'Change Ticket'];
  protected $icon = 'edit';
  protected $table = 'change_ticket';
  protected $hasEntityField = false;
}
