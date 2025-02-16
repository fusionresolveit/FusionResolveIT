<?php

declare(strict_types=1);

namespace App\Models\Forms;

class Answerquestion extends \App\Models\Common
{
  protected $definition = \App\Models\Definitions\Forms\Answerquestion::class;
  protected $titles = ['Answerquestion', 'Answerquestions'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;
}
