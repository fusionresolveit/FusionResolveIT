<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\DefinitionCollection;

class Answer
{
  public static function getDefinition(): DefinitionCollection
  {
    $defColl = new DefinitionCollection();

    return $defColl;
    /*
    [
      'id'    => 15,
      'title' => pgettext('global', 'Creation date'),
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
    return [
      [
        'title' => npgettext('form', 'Answer', 'Answers', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
