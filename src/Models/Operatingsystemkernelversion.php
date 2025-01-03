<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemkernelversion extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemkernelversion';
  protected $titles = ['Kernel version', 'Kernel versions'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'kernel',
  ];

  protected $with = [
    'kernel:id,name',
  ];


  /** @return BelongsTo<\App\Models\Operatingsystemkernel, $this> */
  public function kernel(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Operatingsystemkernel::class, 'operatingsystemkernel_id');
  }
}
