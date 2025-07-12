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
  protected $icon = 'cubes';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'questions',
    'forms',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Section', 'Sections', $nb);
  }

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
