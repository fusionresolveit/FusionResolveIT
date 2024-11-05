<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplate extends Common
{
  protected $definition = '\App\Models\Definitions\Tickettemplate';

  protected $appends = [
    'entity',
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
