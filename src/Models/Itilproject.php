<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Itilproject extends Common
{
  protected $definition = '\App\Models\Definitions\Itilproject';
  protected $titles = ['ITIL Project', 'ITIL Project'];
  protected $icon = 'edit';
  protected $table = 'itil_project';
  protected $hasEntityField = false;
}
