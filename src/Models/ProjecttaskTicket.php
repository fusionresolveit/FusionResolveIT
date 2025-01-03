<?php

declare(strict_types=1);

namespace App\Models;

class ProjecttaskTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ProjecttaskTicket';
  protected $titles = ['Project task', 'Project tasks'];
  protected $icon = 'columns';
  protected $table = 'projecttask_ticket';
  protected $hasEntityField = false;
}
