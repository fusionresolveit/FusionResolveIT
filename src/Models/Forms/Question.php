<?php

declare(strict_types=1);

namespace App\Models\Forms;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends \App\Models\Common
{
  use SoftDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Forms\Question::class;
  protected $titles = ['Question', 'Questions'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;

  /** @return BelongsToMany<\App\Models\Forms\Section, $this> */
  public function sections(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Forms\Section::class);
  }
}
