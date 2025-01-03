<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplatepredefinedfields extends Common
{
  protected $definition = '\App\Models\Definitions\Tickettemplatepredefinedfields';
  protected $titles = ['Ticket template', 'Ticket templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
