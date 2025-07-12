<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Itemdisk extends Common
{
  protected $definition = \App\Models\Definitions\Itemdisk::class;
  protected $icon = 'virus slash';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
    'filesystem:id,name',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('inventory device', 'Volume', 'Volumes', $nb);
  }

  /** @return BelongsTo<\App\Models\Filesystem, $this> */
  public function filesystem(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Filesystem::class);
  }
}
