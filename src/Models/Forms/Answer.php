<?php

declare(strict_types=1);

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends \App\Models\Common
{
  protected $definition = '\App\Models\Definitions\Forms\Answer';
  protected $titles = ['Answer', 'Answers'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;

  /** @return BelongsTo<\App\Models\Forms\Form, $this> */
  public function form(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Forms\Form::class);
  }

  /** @return HasMany<\App\Models\Forms\Answerquestion, $this> */
  public function answerquestions(): HasMany
  {
    return $this->hasMany(\App\Models\Forms\Answerquestion::class);
  }

  /** @return BelongsTo<\App\Models\User, $this> */
  public function user(): BelongsTo
  {
    return $this->belongsTo(\App\Models\User::class);
  }
}
