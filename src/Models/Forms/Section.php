<?php

declare(strict_types=1);

namespace App\Models\Forms;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Section extends \App\Models\Common
{
  use SoftDeletes;
  use CascadesDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Forms\Section::class;
  protected $titles = ['Section', 'Sections'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'questions',
    'forms',
  ];

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
