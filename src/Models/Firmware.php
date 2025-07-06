<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Firmware extends Common
{
  use SoftDeletes;
  use CascadesDeletes;
  use \App\Traits\Relationships\Entity;
  use \App\Traits\Relationships\Documents;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Firmware::class;
  protected $titles = ['Firmware', 'Firmware'];
  protected $icon = 'rom';
  /** @var string[] */
  protected $cascadeDeletes = [
    'documents',
    'itemComputers',
    'itemNetworkequipments',
    'itemPeripherals',
    'itemPhones',
    'itemPrinters',
    'itemStorages',
    'itemEnclosures',
    'itemDeviceprocessors',
  ];

  protected $appends = [
    'modelname',
  ];

  protected $visible = [
    'manufacturer',
    'entity',
    'documents',
    'modelname',
  ];

  protected $with = [
    'manufacturer:id,name',
    'entity:id,name,completename',
    'documents',
  ];

  public function getModelnameAttribute(): string
  {
    $model = $this->getAttribute('model');
    if (class_exists($model))
    {
      $item = new $model();
      if (method_exists($item, 'getTitle'))
      {
        return $item->getTitle();
      }
    }
    return '';
  }

  /** @return BelongsTo<\App\Models\Manufacturer, $this> */
  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Manufacturer::class);
  }

  /** @return HasMany<\App\Models\Computer, $this> */
  public function itemComputers(): HasMany
  {
    return $this->hasMany(\App\Models\Computer::class);
  }

  /** @return HasMany<\App\Models\Networkequipment, $this> */
  public function itemNetworkequipments(): HasMany
  {
    return $this->hasMany(\App\Models\Networkequipment::class);
  }

  /** @return HasMany<\App\Models\Peripheral, $this> */
  public function itemPeripherals(): HasMany
  {
    return $this->HasMany(\App\Models\Peripheral::class);
  }

  /** @return HasMany<\App\Models\Phone, $this> */
  public function itemPhones(): HasMany
  {
    return $this->HasMany(\App\Models\Phone::class);
  }

  /** @return HasMany<\App\Models\Printer, $this> */
  public function itemPrinters(): HasMany
  {
    return $this->HasMany(\App\Models\Printer::class);
  }

  /** @return HasMany<\App\Models\Storage, $this> */
  public function itemStorages(): HasMany
  {
    return $this->HasMany(\App\Models\Storage::class);
  }

  /** @return HasMany<\App\Models\Enclosure, $this> */
  public function itemEnclosures(): HasMany
  {
    return $this->HasMany(\App\Models\Enclosure::class);
  }

  /** @return HasMany<\App\Models\Deviceprocessor, $this> */
  public function itemDeviceprocessors(): HasMany
  {
    return $this->HasMany(\App\Models\Deviceprocessor::class);
  }
}
