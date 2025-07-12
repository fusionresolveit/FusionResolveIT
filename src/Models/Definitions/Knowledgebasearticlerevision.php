<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Knowledgebasearticlerevision
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'article'    => npgettext('knowbase article', 'Article', 'Articles', 1),
      'revision'   => pgettext('knowbase article', 'Revision number'),
      'user'       => npgettext('global', 'User', 'Users', 1),
      'updated_at' => pgettext('global', 'Last update'),
      'created_at' => pgettext('global', 'Creation date'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(1001, $t['article'], 'textarea', 'article', fillable: true));
    $defColl->add(new Def(1002, $t['revision'], 'input', 'revision', fillable: true));
    $defColl->add(new Def(
      1003,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(
      1004,
      $t['article'],
      'dropdown_remote',
      'knowledgebasearticle_id',
      dbname: 'knowledgebasearticle_id',
      itemtype: '\App\Models\Knowledgebasearticle',
      fillable: true
    ));
    $defColl->add(new Def(1005, $t['updated_at'], 'datetime', 'updated_at', readonly: true));
    $defColl->add(new Def(1006, $t['created_at'], 'datetime', 'created_at', readonly: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [];
  }
}
