<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Tickettemplate extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Tickettemplate::class;
  protected $icon = 'edit';
  /** @var string[] */
  protected $cascadeDeletes = [
    'mandatoryfields',
    'predefinedfields',
    'hiddenfields',
  ];

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

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Ticket template', 'Ticket templates', $nb);
  }

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
