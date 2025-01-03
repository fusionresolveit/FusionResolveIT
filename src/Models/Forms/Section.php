<?php

declare(strict_types=1);

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends \App\Models\Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Forms\Section';
  protected $titles = ['Section', 'Sections'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;

  /** @return BelongsToMany<\App\Models\Forms\Question, $this> */
  public function questions(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Forms\Question::class);
  }

  /** @return BelongsToMany<\App\Models\Forms\Form, $this> */
  public function forms(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Forms\Form::class);
  }
}
