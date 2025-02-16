<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\DefinitionCollection;

class Answer
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $defColl = new DefinitionCollection();

    return $defColl;
    /*
    [
      'id'    => 15,
      'title' => $translator->translate('Creation date'),
      'type'  => 'datetime',
      'name'  => 'date_creation',
      'readonly'  => 'readonly',
    ],
    */
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
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
