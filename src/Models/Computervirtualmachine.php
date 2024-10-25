<?php

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

  public function computer(): BelongsTo
  {
    return $this->belongsTo('App\Models\Computer');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('App\Models\Virtualmachinestate', 'virtualmachinestate_id');
  }

  public function system(): BelongsTo
  {
    return $this->belongsTo('App\Models\Virtualmachinesystem', 'virtualmachinesystem_id');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('App\Models\Virtualmachinetype', 'virtualmachinetype_id');
  }
}
