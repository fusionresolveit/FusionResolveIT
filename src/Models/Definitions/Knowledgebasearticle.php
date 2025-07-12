<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Knowledgebasearticle
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'name'       => pgettext('global', 'Name'),
      'category'   => npgettext('global', 'Category', 'Categories', 1),
      'article'    => npgettext('knowbase article', 'Article', 'Articles', 1),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
      'views'      => pgettext('knowbase article', 'Number of views'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(2, $t['name'], 'input', 'name', fillable: true));
    $defColl->add(new Def(
      4,
      $t['category'],
      'dropdown_remote',
      'category',
      dbname: 'category_id',
      itemtype: '\App\Models\Category',
      fillable: true
    ));
    $defColl->add(new Def(1002, $t['views'], 'input', 'views', readonly: true));
    $defColl->add(new Def(1001, $t['article'], 'textarea', 'article', fillable: true));
    $defColl->add(new Def(19, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(121, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
      [
        'title' => npgettext('knowbase article', 'Article', 'Articles', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => pgettext('knowbase article', 'Entity view'),
        'icon' => 'layer group',
        'link' => $rootUrl . '/entityview',
      ],
      [
        'title' => pgettext('knowbase article', 'Group view'),
        'icon' => 'group',
        'link' => $rootUrl . '/groupview',
      ],
      [
        'title' => pgettext('knowbase article', 'Profile view'),
        'icon' => 'user check',
        'link' => $rootUrl . '/profileview',
      ],
      [
        'title' => pgettext('knowbase article', 'User view'),
        'icon' => 'user',
        'link' => $rootUrl . '/userview',
      ],
      [
        'title' => pgettext('knowbase article', 'Revisions'),
        'icon' => 'code branch',
        'link' => $rootUrl . '/revisions',
      ],
    ];
  }
}
