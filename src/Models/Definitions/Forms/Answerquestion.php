<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Answerquestion
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'date_creation' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(15, $t['date_creation'], 'datetime', 'date_creation', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('form', 'Answerquestion', 'Answerquestions', 2),
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
