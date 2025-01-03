<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DomainItem extends Common
{
  protected $definition = '\App\Models\Definitions\DomainItem';
  protected $titles = ['DomainItem', 'DomainItems'];
  protected $icon = 'globe americas';

  protected $table = 'domain_item';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
    'domain:id,name',
    'relation',
  ];

  /** @return BelongsTo<\App\Models\Domain, $this> */
  public function domain(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Domain::class);
  }

  /** @return BelongsTo<\App\Models\Domainrelation, $this> */
  public function relation(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Domainrelation::class, 'domainrelation_id');
  }
}
