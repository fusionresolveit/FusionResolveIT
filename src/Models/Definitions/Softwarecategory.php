<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Softwarecategory
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name' => pgettext('global', 'Name'),
      'category' => pgettext('global', 'As child of'),
      'comment' => npgettext('global', 'Comment', 'Comments', 2),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      12,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'softwarecategory_id',
      itemtype: '\App\Models\Softwarecategory',
      fillable: true
    ));
    $defColl->add(new Def(16, $t['comment'], 'textarea', 'comment', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('global', 'Software category', 'Software categories', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => npgettext('global', 'Software category', 'Software categories', 2),
        'icon' => 'edit',
        'link' => $rootUrl . '/softwarecategories',
      ],
      [
        'title' => npgettext('global', 'Historical', 'Historicals', 1),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
