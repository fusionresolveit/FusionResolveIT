<?php

declare(strict_types=1);

namespace App\Models;

class ProblemTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ProblemTicket';
  protected $titles = ['Problem Ticket', 'Problem Ticket'];
  protected $icon = 'edit';
  protected $table = 'problem_ticket';
  protected $hasEntityField = false;
}
