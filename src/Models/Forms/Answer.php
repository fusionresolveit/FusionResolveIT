<?php

declare(strict_types=1);

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Answer extends \App\Models\Common
{
  use CascadesDeletes;

  protected $definition = \App\Models\Definitions\Forms\Answer::class;
  protected $icon = 'cubes';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'answerquestions',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('form', 'Answer', 'Answers', $nb);
  }

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
