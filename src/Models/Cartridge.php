<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cartridge extends Common
{
  protected $definition = '\App\Models\Definitions\Cartridge';
  protected $titles = ['Cartridge', 'Cartridges'];
  protected $icon = 'fill drip';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'date_in',
    'date_use',
    'date_out',
    'pages',
    'cartridgeitems',
    'printer',
  ];

  protected $with = [
    'cartridgeitems:id,name,cartridgeitemtype_id',
    'printer',
  ];


  /** @return BelongsTo<\App\Models\Cartridgeitem, $this> */
  public function cartridgeitems(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Cartridgeitem::class, 'cartridgeitem_id');
  }

  /** @return BelongsTo<\App\Models\Printer, $this> */
  public function printer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Printer::class, 'printer_id');
  }
}
