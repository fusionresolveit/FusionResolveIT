<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domainrecordtype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Domainrecordtype';
  protected $titles = ['Record type', 'Records types'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];


  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
