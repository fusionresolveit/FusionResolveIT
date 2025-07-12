<?php

declare(strict_types=1);

namespace App\Models\Definitions;

use App\DataInterface\Definition as Def;
use App\DataInterface\DefinitionCollection;

class Menubookmark
{
  public static function getDefinition(): DefinitionCollection
  {
    $t = [
      'user' => npgettext('global', 'User', 'Users', 1),
      'endpoint' => pgettext('global', 'Endpoint'),
    ];

    $defColl = new DefinitionCollection();
    $defColl->add(new Def(
      2,
      $t['user'],
      'dropdown_remote',
      'user',
      dbname: 'user_id',
      itemtype: '\App\Models\User',
      fillable: true
    ));
    $defColl->add(new Def(3, $t['endpoint'], 'input', 'endpoint', fillable: true));

    return $defColl;
  }

  /**
   * @return array<mixed>
   */
  public static function getRelatedPages(string $rootUrl): array
  {
    return [
    ];
  }
}
