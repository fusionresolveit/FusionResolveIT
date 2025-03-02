<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fqdn extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Fqdn::class;
  protected $titles = ['Internet domain', 'Internet domains'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'alias',
  ];

  protected $with = [
    'entity:id,name,completename',
    'alias',
  ];

  /** @return HasMany<\App\Models\Networkalias, $this> */
  public function alias(): HasMany
  {
    return $this->hasMany(\App\Models\Networkalias::class, 'fqdn_id');
  }
}
