<?php

declare(strict_types=1);

namespace App\Models\Definitions\Forms;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Section
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name'));
    $defColl->add(new Def(1001, $t['comment'], 'textarea', 'comment', fillable: true));
    $defColl->add(new Def(16, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(15, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Section', 'Sections', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Form', 'Forms', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => npgettext('global', 'Question', 'Questions', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/questions',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
