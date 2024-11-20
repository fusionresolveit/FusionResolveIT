<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplatehiddenfields extends Common
{
  protected $definition = '\App\Models\Definitions\Tickettemplatehiddenfields';
  protected $titles = ['Ticket template', 'Ticket templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
