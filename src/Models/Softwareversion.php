<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Softwareversion extends Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Softwareversion';
  protected $titles = ['Software version', 'Software versions'];
  protected $icon = 'edit';

  protected $appends = [
  ];

  protected $visible = [
    'software_id',
    'entity',
  ];

  protected $fillable = [
    'name',
    'entity_id',
    'software_id',
    'entity',
  ];

  protected $with = [
    'software:id,name',
    'entity:id,name,completename',
    'state:id,name',
    'operatingsystem:id,name',
  ];


  // We get all devices
  /** @return BelongsToMany<\App\Models\Computer, $this> */
  public function devices(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Computer::class, 'item_softwareversion', 'softwareversion_id', 'item_id');
  }

  /** @return BelongsTo<\App\Models\Software, $this> */
  public function software(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Software::class);
  }

  /** @return BelongsTo<\App\Models\State, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\State::class);
  }

  /** @return BelongsTo<\App\Models\Operatingsystem, $this> */
  public function operatingsystem(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Operatingsystem::class, 'operatingsystem_id');
  }
}
