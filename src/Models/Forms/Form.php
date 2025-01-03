<?php

declare(strict_types=1);

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends \App\Models\Common
{
  use SoftDeletes;
  use \App\Traits\Relationships\Entity;

  protected $definition = '\App\Models\Definitions\Forms\Form';
  protected $titles = ['Form', 'Forms'];
  protected $icon = 'cubes';

  protected $appends = [
  ];

  protected $visible = [
    'category',
    'entity',
  ];

  protected $with = [
    'category:id,name',
    'entity:id,name,completename',
  ];

  /** @return BelongsTo<\App\Models\Category, $this> */
  public function category(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Category::class);
  }

  /** @return BelongsToMany<\App\Models\Forms\Section, $this> */
  public function sections(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Forms\Section::class);
  }
}
