<?php

declare(strict_types=1);

namespace App\Models\Forms;

class Answerquestion extends \App\Models\Common
{
  protected $definition = \App\Models\Definitions\Forms\Answerquestion::class;
  protected $icon = 'cubes';
  protected $hasEntityField = false;

  /**
   * @param $nb int number of elements
   */
  public function getTitle(int $nb = 1): string
  {
    return npgettext('form', 'Answerquestion', 'Answerquestions', $nb);
  }
}
