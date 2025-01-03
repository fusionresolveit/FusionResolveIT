<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Domainrelation extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Domainrelation';
  protected $titles = ['Domain relation', 'Domains relations'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'domains',
  ];

  protected $with = [
    'entity:id,name,completename',
    'domains',
  ];

  /** @return BelongsToMany<\App\Models\Domain, $this> */
  public function domains(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Domain::class, 'domain_item', 'domainrelation_id', 'domain_id');
  }
}
