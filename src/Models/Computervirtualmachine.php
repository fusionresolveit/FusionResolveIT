<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Computervirtualmachine extends Common
{
  use GetDropdownValues;

  protected $table = 'computervirtualmachines';
  protected $definition = \App\Models\Definitions\Computervirtualmachine::class;
  protected $icon = 'virus slash';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
    'state:id,name',
    'system:id,name',
    'type:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Virtual machine', 'Virtual machines', $nb);
  }

  /** @return BelongsTo<\App\Models\Computer, $this> */
  public function computer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Computer::class);
  }

  /** @return BelongsTo<\App\Models\Virtualmachinestate, $this> */
  public function state(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Virtualmachinestate::class, 'virtualmachinestate_id');
  }

  /** @return BelongsTo<\App\Models\Virtualmachinesystem, $this> */
  public function system(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Virtualmachinesystem::class, 'virtualmachinesystem_id');
  }

  /** @return BelongsTo<\App\Models\Virtualmachinetype, $this> */
  public function type(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Virtualmachinetype::class, 'virtualmachinetype_id');
  }
}
