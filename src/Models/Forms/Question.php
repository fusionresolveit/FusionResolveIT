<?php

declare(strict_types=1);

namespace App\Models\Forms;

use App\Traits\GetDropdownValues;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Question extends \App\Models\Common
{
  use SoftDeletes;
  use CascadesDeletes;

  use GetDropdownValues;

  protected $definition = \App\Models\Definitions\Forms\Question::class;
  protected $icon = 'cubes';
  protected $hasEntityField = false;
  /** @var string[] */
  protected $cascadeDeletes = [
    'sections',
  ];

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('global', 'Question', 'Questions', $nb);
  }

  /** @return BelongsToMany<\App\Models\Forms\Section, $this> */
  public function sections(): BelongsToMany
  {
    return $this->belongsToMany(\App\Models\Forms\Section::class);
  }
}
