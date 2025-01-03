<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

class Answerquestion
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 15,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'date_creation',
        'readonly'  => 'readonly',
      ],
    ];
  }

  public static function getRelatedPages($rootUrl): array
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Answerquestion', 'Answerquestions', 2),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
