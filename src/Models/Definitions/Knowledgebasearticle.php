<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Knowledgebasearticle
{
  public static function getDefinition(): DefinitionCollection
  {
    global $translator;

    $t = [
      'name'       => $translator->translate('Name'),
      'category'   => $translator->translate('Category'),
      'article'    => $translator->translate('Article'),
      'updated_at' => $translator->translate('Last update'),
      'created_at' => $translator->translate('Creation date'),
      'views'      => $translator->translate('Number of views'),
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
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Article', 'Articles', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Entity view'),
        'icon' => 'layer group',
        'link' => $rootUrl . '/entityview',
      ],
      [
        'title' => $translator->translate('Group view'),
        'icon' => 'group',
        'link' => $rootUrl . '/groupview',
      ],
      [
        'title' => $translator->translate('profile view'),
        'icon' => 'user check',
        'link' => $rootUrl . '/profileview',
      ],
      [
        'title' => $translator->translate('User view'),
        'icon' => 'user',
        'link' => $rootUrl . '/userview',
      ],
      [
        'title' => $translator->translate('Revisions'),
        'icon' => 'code branch',
        'link' => $rootUrl . '/revisions',
      ],
    ];
  }
}
