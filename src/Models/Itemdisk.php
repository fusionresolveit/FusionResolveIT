<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itemdisk extends Common
{
  protected $definition = \App\Models\Definitions\Itemdisk::class;
  protected $titles = ['Volume', 'Volumes'];
  protected $icon = 'virus slash';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
    'filesystem:id,name',
  ];

  /** @return BelongsTo<\App\Models\Filesystem, $this> */
  public function filesystem(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Filesystem::class);
  }
}
