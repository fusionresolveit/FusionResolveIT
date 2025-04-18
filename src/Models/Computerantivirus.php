<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computerantivirus extends Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $table = 'computerantiviruses';
  protected $definition = \App\Models\Definitions\Computerantivirus::class;
  protected $titles = ['Antivirus', 'Antivirus'];
  protected $icon = 'virus slash';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'manufacturer',
  ];

  protected $with = [
    'manufacturer:id,name',
  ];

  protected $fillable = [
    'name',
    'computer_id',
  ];

  protected $casts = [
    'is_active'  => 'boolean',
    'is_dynamic' => 'boolean',
  ];

  /** @return BelongsTo<\App\Models\Computer, $this> */
  public function computer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Computer::class);
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }
}
