<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickettemplate extends Common
{
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Tickettemplate';
  protected $titles = ['Ticket template', 'Ticket templates'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'entity',
    'mandatoryfields',
    'predefinedfields',
    'hiddenfields',
  ];

  protected $with = [
    'entity:id,name,completename',
    'mandatoryfields:id,num',
    'predefinedfields:id,num,value',
    'hiddenfields:id,num',
  ];

  protected $fillable = [
    'name',
  ];

  /** @return HasMany<\App\Models\Tickettemplatemandatoryfields, $this> */
  public function mandatoryfields(): HasMany
  {
    return $this->hasMany(\App\Models\Tickettemplatemandatoryfields::class, 'tickettemplate_id');
  }

  /** @return HasMany<\App\Models\Tickettemplatepredefinedfields, $this> */
  public function predefinedfields(): HasMany
  {
    return $this->hasMany(\App\Models\Tickettemplatepredefinedfields::class, 'tickettemplate_id');
  }

  /** @return HasMany<\App\Models\Tickettemplatehiddenfields, $this> */
  public function hiddenfields(): HasMany
  {
    return $this->hasMany(\App\Models\Tickettemplatehiddenfields::class, 'tickettemplate_id');
  }
}
