<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datacenter extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Location;

  protected $definition = '\App\Models\Definitions\Datacenter';
  protected $titles = ['Data center', 'Data centers'];
  protected $icon = 'warehouse';

  protected $appends = [
  ];

  protected $visible = [
    'location',
    'entity',
    'dcrooms',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
    'dcrooms',
  ];

  /** @return HasMany<\App\Models\Dcroom, $this> */
  public function dcrooms(): HasMany
  {
    return $this->hasMany(\App\Models\Dcroom::class);
  }
}
