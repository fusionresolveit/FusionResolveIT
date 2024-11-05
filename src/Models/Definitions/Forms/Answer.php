<?php

namespace App\Models\Definitions\Forms;

class Answer
{
  public static function getDefinition()
  {
    global $translator;
    return [
      /*
      [
        'id'    => 15,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'date_creation',
        'readonly'  => 'readonly',
      ],
      */
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Answer', 'Answers', 1),
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
