<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class LinkItemtype extends Common
{
  use PivotEventTrait;

  protected $definition = \App\Models\Definitions\LinkItemtype::class;
  protected $icon = 'virus slash';

  protected $table = 'link_itemtype';

  protected $appends = [
  ];

  protected $visible = [
    'links',
  ];

  protected $with = [
    'links:id,name',
  ];

  /** @return BelongsTo<\App\Models\Link, $this> */
  public function links(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Link::class, 'link_id');
  }

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Link Itemtype', 'Link Itemtype', $nb);
  }
}
