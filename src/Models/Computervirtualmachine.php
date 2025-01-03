<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Computervirtualmachine extends Common
{
  protected $table = 'computervirtualmachines';
  protected $definition = '\App\Models\Definitions\Computervirtualmachine';
  protected $titles = ['Virtual machine', 'Virtual machines'];
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
