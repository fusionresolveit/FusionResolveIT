<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profilerightcustom extends Common
{
  protected $definition = '\App\Models\Definitions\Profilerightcustom';
  protected $titles = ['Profile', 'Profiles'];
  protected $icon = 'user check';
  protected $hasEntityField = false;

  protected $fillable = [
    'profileright_id',
    'definitionfield_id',
    'read',
    'write',
  ];
}
