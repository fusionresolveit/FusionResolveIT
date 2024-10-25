<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Networkname extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Networkname';
  protected $titles = ['Network name', 'Network names'];
  protected $icon = 'edit';

  protected $appends = [
    'fqdn',
    'entity',
  ];

  protected $visible = [
    'fqdn',
    'entity',
  ];

  protected $with = [
    'fqdn:id,name',
    'entity:id,name',
  ];

  public function fqdn(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Fqdn');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
